<?php

namespace App\Http\Controllers;

use App\Models\GrantPrivilege;
use App\Models\ActionButton;
use App\Models\ActiveLeadSource;
use App\Models\RegisteredLead;
use App\Models\Group;
use App\Models\Team;
use App\Models\User;
use App\Models\SidebarMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userGroups()
    {
        $groups = DB::table('groups')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('user.groups', compact('groups'));
    }
    // fetch Zones
    public function fetchZones()
    {
        try {
            // Fetch distinct zones from the location table
            $zones = DB::table('users')
                ->distinct()
                ->pluck('zone'); // returns an array of zone values

            // Return JSON response
            return response()->json(['zones' => $zones]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching zones: ' . $e->getMessage());

            // Return error response
            return response()->json(['error' => 'Unable to fetch zones'], 500);
        }
    }
    // Fetch Counselors
    public function fetchCounselors(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'zone' => 'required|string'
            ]);

            $zone = $request->input('zone');

            // Fetch employees based on zone and status
            $counselors = DB::table('users')
                ->select('employee_code', 'employee_name')
                ->where('zone', $zone)
                ->where('status', 'TRUE')
                ->get();

            // Transform data into required format
            $formattedCounselors = $counselors->map(function ($item) {
                return $item->employee_code . '*' . $item->employee_name;
            });
            // Return JSON response
            return response()->json(['counselors' => $formattedCounselors]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'zone parameter is missing or invalid'], 422);
        } catch (\Exception $e) {
            Log::error('Error fetching counselors: ' . $e->getMessage());
            return response()->json(['error' => 'Database query failed'], 500);
        }
    }
    // Store Group Info
    public function storeGroups(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'group_name' => 'nullable|string|max:255|unique:groups,group_name',
            'group_zone' => 'nullable|string|max:255',
            'group_leader' => 'nullable|string'
        ]);

        try {
            // Get authenticated user details
            $user = Auth::user();

            if (!$user || !$user->employee_code || !$user->employee_name) {
                return response()->json(['status' => 'error', 'message' => 'Employee details not set.'], 401);
            }

            $activeUser = $user->employee_code . '*' . $user->employee_name;

            DB::beginTransaction();

            // Insert into groups table
            $groupId = DB::table('groups')->insertGetId([
                'group_name' => $validated['group_name'],
                'group_zone' => $validated['group_zone'],
                'group_leader' => $validated['group_leader'],
                'created_by' => $activeUser,
                'updated_by' => $activeUser,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Extract employee_code from group_leader
            $parts = explode('*', $validated['group_leader']);
            $employeeCodeToUpdate = trim($parts[0]);

            // Update user_category in users table
            DB::table('users')
                ->where('employee_code', $employeeCodeToUpdate)
                ->update(['user_category' => 'Group Leader']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Group added successfully and user category updated.',
                'data' => [
                    'group_id' => $groupId,
                    'created_by' => $activeUser
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving data: ' . $e->getMessage()
            ], 500);
        }
    }
    // fetch Group data
    public function fetchGroupData(Request $request)
    {
        // Validate ID
        $request->validate([
            'id' => 'required|string',
        ]);

        // Fetch the group by ID
        $group = Group::find($request->id);

        if ($group) {
            return response()->json([
                'status' => 'success',
                'group' => [
                    'id' => $group->id,
                    'group_name' => $group->group_name,
                    'group_zone' => $group->group_zone,
                    'group_leader' => $group->group_leader
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Group not found'
            ], 404);
        }
    }
    // edit group info
    public function updateGroup(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|string',
            'group_name' => 'required|string|max:255',
            'group_zone' => 'required|string|max:255',
            'group_leader' => 'required|string',
        ]);

        $user = Auth::user();
        $activeUser = $user->employee_code . '*' . $user->employee_name;

        try {
            DB::beginTransaction();

            // Find the group
            $group = Group::findOrFail($request->id);

            $previousLeader = $group->group_leader;

            // Update the group details
            $group->update([
                'group_name' => $request->group_name,
                'group_zone' => $request->group_zone,
                'group_leader' => $request->group_leader,
                'updated_by' => $activeUser,
            ]);

            // Extract employee_code from group_leader
            $parts = explode('*', $request->group_leader);
            $employee_code_to_update = trim($parts[0]);

            // Update user_category in users table
            User::where('employee_code', $employee_code_to_update)
                ->update(['user_category' => 'Group Leader']);

            // If previous leader is different, reset their category
            if ($previousLeader !== $request->group_leader) {
                $oldLeaderParts = explode('*', $previousLeader);
                $oldLeaderCode = trim($oldLeaderParts[0]);

                User::where('employee_code', $oldLeaderCode)
                    ->update(['user_category' => null]); // or default category
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group and user category updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    // view Connected Teams
    public function viewConnectedTeams($encoded)
    {
        $decoded = base64_decode($encoded);

        [$id, $name] = explode('*', $decoded);
        // Fetch teams from the database ordered by updated_at
        $teams = DB::table('teams')
            ->where('group_name', $name)
            ->orderBy('updated_at', 'DESC')
            ->get();

        // Pass the teams to the view using compact
        return view('user.view_teams', compact('teams'));
    }
    // teams mapping
    public function teamMapping($encoded)
    {
        $decoded = base64_decode($encoded);

        [$id, $name, $zone, $leaderCode, $leaderName] = explode('*', $decoded);

        // Fetch teams from the database ordered by updated_at
        $teams = DB::table('teams')
            ->orderBy('updated_at', 'DESC')
            ->get();

        // Pass the teams to the view using compact
        return view('user.team_mapping', compact('id', 'name', 'zone', 'leaderCode', 'leaderName', 'teams'));
    }
    // update Group name for team
    public function teamMappingUpdation(Request $request)
    {
        // Validate input
        $request->validate([
            'selected_group' => 'required|string',
            'selected_teams' => 'required|array',
            'selected_teams.*' => 'integer|exists:teams,id'
        ]);

        // Extract group name from selected_group (format: id*name)
        $groupParts = explode('*', $request->selected_group);
        $groupName = $groupParts[1] ?? null;

        if (!$groupName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid group format.'
            ], 400);
        }

        // Update all selected teams
        Team::whereIn('id', $request->selected_teams)
            ->update(['group_name' => trim($groupName)]);

        return response()->json([
            'status' => 'success',
            'message' => 'Group updated successfully'
        ]);
    }
    // teams Blade
    public function userTeams()
    {
        $teams = DB::table('teams')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('user.teams', compact('teams'));
    }
    // fetch all Counselors
    public function fetchAllCounselors()
    {
        $counselors = User::select('employee_code', 'employee_name')
            ->get()
            ->map(function ($user) {
                return $user->employee_code . '*' . $user->employee_name;
            });

        return response()->json([
            'counselors' => $counselors
        ]);
    }
    // Store Teams
    public function storeTeams(Request $request)
    {
        // ✅ Validate request
        $validated = $request->validate([
            'team_name' => 'required|string|max:255|unique:teams,team_name',
            'team_leader' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if (!$user || !$user->employee_code || !$user->employee_name) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee details not set.'
            ], 401);
        }

        $activeUser = $user->employee_code . '*' . $user->employee_name;

        // ✅ Create the new team
        $team = new Team();
        $team->team_name = trim($validated['team_name']);
        $team->team_leader = trim($validated['team_leader']);
        $team->created_by = $activeUser;
        $team->updated_by = $activeUser;
        $team->save();

        // ✅ Extract employee_code from "code * name"
        $parts = explode('*', $validated['team_leader']);
        $employee_code_to_update = trim($parts[0] ?? '');

        if ($employee_code_to_update) {
            User::where('employee_code', $employee_code_to_update)
                ->update(['user_category' => 'Team Leader']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Team added successfully and user category updated.',
            'team' => $team
        ]);
    }
    // Fetch teams Data
    // public function fetchTeamData(Request $request)
    // {
    //     // Validate ID
    //     $request->validate([
    //         'id' => 'required|string',
    //     ]);

    //     // Fetch the group by ID
    //     $team = Team::find($request->id);

    //     if ($team) {
    //         return response()->json([
    //             'status' => 'success',
    //             'team' => [
    //                 'id' => $team->id,
    //                 'group_name' => $team->team_name,
    //                 'group_leader' => $team->team_leader
    //             ]
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Team not found'
    //         ], 404);
    //     }
    // }
    // edit team info
    // public function updateTeam(Request $request)
    // {
    //     $validated = $request->validate([
    //         'team_id' => 'required|string',
    //         'team_name' => 'required|string',
    //         'team_leader' => 'required|string',
    //     ]);

    //     $user = Auth::user();
    //     $activeUser = $user->employee_code . '*' . $user->employee_name;

    //     try {
    //         DB::beginTransaction();

    //         $team = Team::findOrFail($validated['team_id']);
    //         $previousLeader = $team->team_leader;

    //         $team->team_name = $validated['team_name'];
    //         $team->team_leader = $validated['team_leader'];
    //         $team->updated_by = $activeUser;
    //         $team->save();

    //         // Update new leader category
    //         $parts = explode('*', $validated['team_leader']);
    //         $employee_code_to_update = trim($parts[0] ?? '');
    //         if (!empty($employee_code_to_update)) {
    //             User::where('employee_code', $employee_code_to_update)
    //                 ->update(['user_category' => 'Team Leader - ' . $validated['team_name']]);
    //         }

    //         // Reset old leader category if changed
    //         if (trim($previousLeader) !== trim($validated['team_leader'])) {
    //             $oldLeaderCode = trim(explode('*', $previousLeader)[0] ?? '');
    //             if (!empty($oldLeaderCode)) {
    //                 User::where('employee_code', $oldLeaderCode)
    //                     ->update(['user_category' => null]); // or default category
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Team and user category updated successfully'
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error updating team: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    // View and Mange Users
    public function viewConnectedUsers($encoded)
    {
        // Decode the base64 parameter
        $encodedValue = $encoded;
        $decoded = base64_decode($encoded);
        [$id, $teamName] = explode('*', $decoded);

        // Fetch the team by ID (optional, if you need it)
        $team = Team::find($id);

        // Fetch users that belong to this team
        $users = User::where('team_name', $teamName)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $activeSources = ActiveLeadSource::where('status', 'Active')->pluck('sources')->toArray();


        // Pass the team and users to the Blade view
        return view('user.view_members', compact('team', 'users', 'teamName', 'activeSources', 'encodedValue'));
    }
    // Team members Mapping
    public function UsersMapping($encoded)
    {
        $decoded = base64_decode($encoded);

        [$id, $name, $leaderCode, $leaderName, $group] = explode('*', $decoded);

        // Fetch teams from the database ordered by updated_at
        $users = DB::table('users')
            ->where('team_name', $name)
            ->orderBy('updated_at', 'DESC')
            ->get();

        // Pass the teams to the view using compact
        return view('user.user_mapping', compact('id', 'name', 'leaderCode', 'leaderName', 'group', 'users'));
    }
    // AJAX search users
    public function searchUsers(Request $request)
    {
        $term = $request->get('q');
        return DB::table('users')
            ->whereNull('team_name') // only unassigned
            ->whereNotIn('user_category', ['Super Admin', 'Team Leader', 'Group Leader'])
            ->where(function ($q) use ($term) {
                $q->where('employee_name', 'like', "%$term%")
                    ->orWhere('employee_code', 'like', "%$term%");
            })
            ->select('id', 'employee_code', 'employee_name')
            ->get();
    }
    // Add user to team
    public function addUserToTeam(Request $request)
    {
        $ids = $request->ids; // Array of user IDs
        $teamName = $request->team_name;
        $user = Auth::user();
        $authUser = $user->employee_code . '*' . $user->employee_name;

        $updatedCount = DB::table('users')->whereIn('id', $ids)
            ->update(['team_name' => $teamName, 'user_category' => 'Counsellor', 'updated_at' => now(), 'updated_by' => $authUser]);

        return response()->json(['status' => 'success', 'message' => "$updatedCount users added to team $teamName"]);
    }
    // Remove user from team
    public function removeUserFromTeam(Request $request)
    {
        DB::table('users')->where('id', $request->id)
            ->update(['team_name' => null]);

        return response()->json(['status' => 'success']);
    }
    //Users Blade
    public function users()
    {
        return view('user.users');
    }
    // Create User 
    public function createUser()
    {
        $department = User::whereNotNull('department')->distinct()->pluck('department');

        $zone = User::whereNotNull('zone')->distinct()->pluck('zone');

        return view('user.createUser', compact('department', 'zone'));
    }

    public function storeUser(Request $request)
    {
        $authUser = Auth::user();

        // Validate request
        $validator = Validator::make($request->all(), [
            'employeeCode' => 'required|string|unique:users,employee_code',
            'employeeName' => 'required|string|max:255',
            'email' => 'required|string',
            'mobile' => 'required|string|max:15',
            'dob' => 'required|string',
            'gender' => 'required|string',
            'pan' => 'required|string',
            'department' => 'required|string',
            'designation' => 'required|string',
            'zone' => 'nullable|string',
            'branch' => 'nullable|string',
            'doj' => 'required|string',
            'officialEmail' => 'nullable|string',
            'officialMobile' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create user
        $user = User::create([
            'employee_code' => $request->employeeCode,
            'employee_name' => $request->employeeName,
            'email_id_personal' => $request->email,
            'mobile_no_personal' => $request->mobile,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'pan_card_no' => $request->pan,
            'department' => $request->department,
            'job_title_designation' => $request->designation,
            'zone' => $request->zone,
            'branch' => $request->branch,
            'doj' => $request->doj,
            'email_id_official' => $request->officialEmail,
            'mobile_no_official' => $request->officialMobile,
            'created_at' => now(),
            'created_by' => $authUser->employee_code . '*' . $authUser->employee_name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function userPrivileges()
    {
        return view('user.privileges');
    }

    public function addSidebarMenus()
    {
        $sidebarMenus = SidebarMenu::select('categories', 'icons')->distinct()->get();

        return view('user.add_sidebar_menus', compact('sidebarMenus'));
    }

    public function storeSidebarMenus(Request $request)
    {
        $request->validate([
            'sidebarName' => 'required|string|max:255',
            'sidebarUrl' => 'required|string|max:255',
            'sidebarIcon' => 'required|string|max:255',
        ]);

        $category = $request->add_new_category
            ? $request->newSidebarCategory
            : $request->existedSidebarCategory;

        $menu = SidebarMenu::create([
            'name' => $category,
            'icons' => $request->sidebarIcon,
            'categories' => $request->sidebarName,
            'url' => $request->sidebarUrl,
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sidebar menu added successfully!',
            'data' => $menu
        ]);
    }

    public function addActionButtons()
    {
        return view('user.add_action_buttons');
    }

    public function storeActionButton(Request $request)
    {
        $request->validate([
            'buttonTitleName' => 'required|string|max:255',
            'buttonIcon' => 'required|string|max:255',
            'buttonClass' => 'required|string|max:255',
            'buttonCategory' => 'required|string|max:255',
            'buttonpurpose' => 'nullable|string',
        ]);

        $buttons = ActionButton::create([
            'name' => $request->buttonTitleName,
            'icon' => $request->buttonIcon,
            'class' => $request->buttonClass,
            'categories' => $request->buttonCategory,
            'purpose' => $request->buttonpurpose,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Action button created successfully!',
            'data' => $buttons,
        ]);
    }

    public function createUserPrivileges()
    {
        return view('user.create_user_privileges');
    }

    public function storeUserPrivilege(Request $request)
    {

        $authUser = Auth::user();

        try {
            // ✅ Validate required fields
            $request->validate([
                'privilegeName' => 'required|string|max:255',
                'menubar_items' => 'required|array|min:1', // at least one required
                'action_items' => 'nullable|array',
            ]);
            // dd($request->all);

            // ✅ Prepare form data
            $priGroupName = $request->privilegeName;
            $actionButtons = $request->action_items ? implode(',', $request->action_items) : '';
            $menubarItems = $request->menubar_items ? implode(',', $request->menubar_items) : '';

            // ✅ Created / Updated by (from logged-in user)


            // ✅ Insert into DB
            $privilege = new GrantPrivilege();
            $privilege->pri_group_name = $priGroupName;
            $privilege->action_buttons = $actionButtons;
            $privilege->menubar_items = $menubarItems;
            $privilege->created_by = $authUser->employee_code . '*' . $authUser->employee_name;
            $privilege->updated_by = $authUser->employee_code . '*' . $authUser->employee_name;
            ;
            $privilege->created_at = Carbon::now();
            $privilege->updated_at = Carbon::now();
            $privilege->save();

            // ✅ Success response
            return response()->json([
                'success' => true,
                'message' => 'Privileges created successfully',
                'data' => $privilege
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function activeLeadSources()
    {
        $activeLeads = ActiveLeadSource::orderBy('created_at', 'desc')->get();

        return view('user.active_lead_sources', compact('activeLeads'));
    }

    public function manageLeadSources()
    {
        // Fetch all active sources from lead_source table
        $activeSources = ActiveLeadSource::where('status', 'Active')->pluck('sources')->toArray();

        // Fetch all distinct registered lead sources
        $registeredSources = RegisteredLead::select('lead_source')
            ->wherenotnull('lead_source')
            ->distinct()
            ->pluck('lead_source')
            ->toArray();

        return view('user.manage_lead_sources', compact('activeSources', 'registeredSources'));
    }

    public function updateLeadSources(Request $request)
    {
        try {
            // ✅ Validate input
            $request->validate([
                'leads' => 'required|array|min:1',
                'leads.*' => 'string|max:255'
            ]);

            $leadSources = $request->input('leads', []);

            // ✅ Logged-in user (Laravel Auth)
            $authUser = Auth::user();
            $createdBy = $authUser->employee_code . '*' . $authUser->employee_name;

            // ✅ Get all existing sources
            $existingSources = ActiveLeadSource::pluck('sources')->toArray();

            // ✅ Insert or update selected leads
            foreach ($leadSources as $lead) {
                if (in_array($lead, $existingSources)) {
                    // Update existing
                    ActiveLeadSource::where('sources', $lead)->update([
                        'status' => 'Active',
                        'created_by' => $createdBy,
                        'updated_at' => now()
                    ]);
                } else {
                    // Insert new
                    ActiveLeadSource::create([
                        'sources' => $lead,
                        'status' => 'Active',
                        'created_by' => $createdBy,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]);
                }
            }

            // ✅ Deactivate unselected leads
            $unselected = array_diff($existingSources, $leadSources);
            if (!empty($unselected)) {
                ActiveLeadSource::whereIn('sources', $unselected)->update([
                    'status' => 'Inactive',
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Lead sources updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function manageTeamMembers($encoded)
    {
        $decoded = base64_decode($encoded);
        [$id, $teamName] = explode('*', $decoded);

        $team = Team::find($id);

        $leader = $team->team_leader;

        $activeSources = ActiveLeadSource::where('status', 'Active')->pluck('sources')->toArray();

        return view('user.manage_team_members', compact('team', 'teamName', 'leader', 'activeSources'));
    }

    public function fetchteamInfo(Request $request)
    {
        try {
            // Validate request input
            $validated = $request->validate([
                'team_name' => 'required|string',
                'tabValue' => 'required|string',
            ]);

            $teamName = $validated['team_name'];
            $tabValue = $validated['tabValue'];

            // Base query
            $query = DB::table('users')
                ->where('team_name', $teamName)
                ->where('status', 'TRUE');

            // Select columns based on tabValue
            switch ($tabValue) {
                case 'Active Users':
                    $query->select('employee_code', 'employee_name', 'working_status', 'int_flag', 'enable_calling');
                    break;

                case 'Access Level':
                    $query->select('employee_code', 'employee_name', 'user_category');
                    break;

                case 'Email & Phone Configuration':
                    $query->select(
                        'employee_code',
                        'employee_name',
                        'telegram_token',
                        'telegram_chat_id',
                        'telegram_channel_name',
                        'mobile_no_personal',
                        'script'
                    );
                    break;

                case 'Lead Sources':
                    $query->select('employee_code', 'employee_name', 'lead_sources');
                    break;

                default:
                    $query->select('employee_code', 'employee_name');
                    break;
            }

            $users = $query->get();

            // Handle empty result
            if ($users->isEmpty()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No group found with the given name',
                    'data' => []
                ], 404);
            }

            // Success response
            return response()->json([
                'ok' => true,
                'message' => 'Team data fetched successfully',
                'data' => $users
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error fetching team info: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateTeamInfo(Request $request)
    {
        $request->validate([
            'team_id' => 'required',
            'team_name' => 'required|string|max:255',
            'active_users' => 'nullable|array',
            'lead_sources' => 'nullable|array',
            'access_level' => 'nullable|array',
            'configuration' => 'nullable|array',
        ]);

        $teamId = $request->input('team_id');
        $teamName = $request->input('team_name');
        $updatedBy = Auth::user()->employee_code ?? 'system';
        $currentDateTime = Carbon::now();

        $updatedCount = 0;

        DB::beginTransaction();

        try {
            // 1. Active Users
            if ($request->filled('active_users')) {
                foreach ($request->input('active_users') as $employeeCode => $flags) {
                    $updatedCount += DB::table('users')
                        ->where('employee_code', $employeeCode)
                        ->where('team_name', $teamName)
                        ->update([
                            'working_status' => isset($flags['general']) ? (int) $flags['general'] : 0,
                            'int_flag' => isset($flags['international']) ? (int) $flags['international'] : 0,
                            'enable_calling' => isset($flags['calling']) ? (int) $flags['calling'] : 0,
                            'updated_by' => $updatedBy,
                            'updated_at' => $currentDateTime,
                        ]);
                }
            }

            // 2. Lead Sources
            if ($request->filled('lead_sources')) {
                foreach ($request->input('lead_sources') as $employeeCode => $sources) {
                    DB::table('users')
                        ->where('employee_code', $employeeCode)
                        ->where('team_name', $teamName)
                        ->update([
                            'lead_sources' => $sources,
                            'updated_by' => $updatedBy,
                            'updated_at' => $currentDateTime,
                        ]);
                    $updatedCount++;
                }
            }

            // 3. Access Level
            if ($request->filled('access_level')) {

                // Get current Team Leader in DB
                $teamLeaderAssigned = DB::table('users')
                    ->where('team_name', $teamName)
                    ->where('user_category', 'Team Leader')
                    ->pluck('employee_code') // existing leader(s)
                    ->toArray();

                $newLeaderInRequest = null; // to track new leader in current request

                foreach ($request->input('access_level') as $employeeCode => $newLevel) {
                    if (!in_array($newLevel, ['Team Leader', 'Counsellor'])) {
                        continue; // skip invalid values
                    }

                    if ($newLevel === 'Team Leader') {
                        // Check if another Team Leader already exists in DB or in this request
                        if (!empty($teamLeaderAssigned) || $newLeaderInRequest !== null) {
                            return response()->json([
                                'success' => false,
                                'message' => "Cannot assign Team Leader to {$employeeCode}. Another Team Leader already exists."
                            ]);
                        }

                        $newLeaderInRequest = $employeeCode; // mark this as new leader
                    }

                    // Update user_category
                    $updatedCount += DB::table('users')
                        ->where('employee_code', $employeeCode)
                        ->where('team_name', $teamName)
                        ->update([
                            'user_category' => $newLevel,
                            'updated_by' => $updatedBy,
                            'updated_at' => $currentDateTime,
                        ]);
                }

                // If a new Team Leader was assigned successfully, update the teams table
                if ($newLeaderInRequest) {
                    // Fetch employee name for the new leader
                    $employee = DB::table('users')
                        ->where('employee_code', $newLeaderInRequest)
                        ->select('employee_name')
                        ->first();

                    if ($employee) {
                        $teamLeaderValue = $newLeaderInRequest . '*' . $employee->employee_name;

                        DB::table('teams')
                            ->where('team_name', $teamName)
                            ->update([
                                'team_leader' => $teamLeaderValue,
                                'updated_by' => $updatedBy,
                                'updated_at' => $currentDateTime,
                            ]);
                    }
                }
            }


            // 4. Email & Phone Configuration
            if ($request->filled('configuration')) {
                foreach ($request->input('configuration') as $employeeCode => $config) {
                    $updatedCount += DB::table('users')
                        ->where('employee_code', $employeeCode)
                        ->where('team_name', $teamName)
                        ->update([
                            'telegram_token' => $config['telegram_token'] ?? null,
                            'telegram_chat_id' => $config['telegram_chat_id'] ?? null,
                            'telegram_channel_name' => $config['telegram_channel_name'] ?? null,
                            'script' => $config['script'] ?? null,
                            'mobile_no_personal' => $config['mobile_no_official'] ?? null,
                            'updated_by' => $updatedBy,
                            'updated_at' => $currentDateTime,
                        ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $updatedCount > 0 ? "$updatedCount records updated successfully" : 'No changes were made'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating team data: ' . $e->getMessage()
            ]);
        }
    }

}
