<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UserController extends Controller
{
    public function userGroups()
    {
        $groups = DB::table('groups')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('user.groups', compact('groups'));
    }

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

    public function userTeams()
    {
        $teams = DB::table('teams')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('user.teams', compact('teams'));
    }

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

    public function fetchTeamData(Request $request)
    {
        // Validate ID
        $request->validate([
            'id' => 'required|string',
        ]);

        // Fetch the group by ID
        $team = Team::find($request->id);

        if ($team) {
            return response()->json([
                'status' => 'success',
                'team' => [
                    'id' => $team->id,
                    'group_name' => $team->team_name,
                    'group_leader' => $team->team_leader
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Team not found'
            ], 404);
        }
    }

    public function updateTeam(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|string',
            'team_name' => 'required|string',
            'team_leader' => 'required|string',
        ]);

        $user = Auth::user();
        $activeUser = $user->employee_code . '*' . $user->employee_name;

        try {
            DB::beginTransaction();

            $team = Team::findOrFail($validated['team_id']);
            $previousLeader = $team->team_leader;

            $team->team_name = $validated['team_name'];
            $team->team_leader = $validated['team_leader'];
            $team->updated_by = $activeUser;
            $team->save();

            // Update new leader category
            $parts = explode('*', $validated['team_leader']);
            $employee_code_to_update = trim($parts[0] ?? '');
            if (!empty($employee_code_to_update)) {
                User::where('employee_code', $employee_code_to_update)
                    ->update(['user_category' => 'Team Leader - ' . $validated['team_name']]);
            }

            // Reset old leader category if changed
            if (trim($previousLeader) !== trim($validated['team_leader'])) {
                $oldLeaderCode = trim(explode('*', $previousLeader)[0] ?? '');
                if (!empty($oldLeaderCode)) {
                    User::where('employee_code', $oldLeaderCode)
                        ->update(['user_category' => null]); // or default category
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team and user category updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating team: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewConnectedUsers($encoded)
    {
        // Decode the base64 parameter
        $decoded = base64_decode($encoded);
        [$id, $teamName] = explode('*', $decoded);

        // Fetch the team by ID (optional, if you need it)
        $team = Team::find($id);

        // Fetch users that belong to this team
        $users = User::where('team_name', $teamName)
            ->orderBy('updated_at', 'DESC')
            ->get();

        // Pass the team and users to the Blade view
        return view('user.view_members', compact('team', 'users', 'teamName'));
    }

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

    // AJAX search available users
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

        DB::table('users')->whereIn('id', $ids)
            ->update(['team_name' => $teamName]);

        return response()->json(['status' => 'success']);
    }

    // Remove user from team
    public function removeUserFromTeam(Request $request)
    {
        DB::table('users')->where('id', $request->id)
            ->update(['team_name' => null]);

        return response()->json(['status' => 'success']);
    }
    public function Users()
    {
        return view('user.users');
    }

}
