<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // dd('Login attempt', $request->all());
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            // Get user with their privileges - use the User model instead of DB::table
            $user = \App\Models\User::where('employee_code', $username)
                ->where('pan_card_no', $password)
                ->where('status', 'TRUE')
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ]);
            }

            // --- LOG THE USER IN FIRST ---
            Auth::login($user);

            // dd('User logged in', $user->toArray());
            // --- NOW ADD YOUR CUSTOM SESSION DATA ---
            $userCode = $user->employee_code . "*" . $user->employee_name;

            // Store user data in session
            if ($user->user_category === 'Group Leader') {
                // 1) Get all group names led by this leader
                $groupNames = DB::table('groups')
                    ->where('group_leader', $userCode)   // if $userCode is a single value
                    ->pluck('group_name');               // Collection of names

                // 2) Get team names under those groups
                $teamNames = $groupNames->isNotEmpty()
                    ? DB::table('teams')
                    ->whereIn('group_name', $groupNames)
                    ->pluck('team_name')
                    : collect();

                // 3) Get users in those teams
                $teamMembers = $teamNames->isNotEmpty()
                    ? DB::table('users')
                    ->whereIn('team_name', $teamNames)
                    ->get()
                    : collect();

                // Optional: put in session
                session([
                    'group_names'  => $groupNames->all(),
                    'team_names'   => $teamNames->all(),
                    'team_members' => $teamMembers, // full rows
                ]);
            } else if ($user->user_category === 'Team Leader') {
                // 1) Get all group names led by this leader
                $teamNames = DB::table('teams')
                    ->where('team_leader', $userCode)   // if $userCode is a single value
                    ->pluck('group_name');               // Collection of names

                // 3) Get users in those teams
                $teamMembers = $teamNames->isNotEmpty()
                    ? DB::table('users')
                    ->whereIn('team_name', $teamNames)
                    ->select('employee_name', 'employee_code')
                    ->get()
                    : collect();

                // Optional: put in session
                session([
                    'team_names'   => $teamNames->all(),
                    'team_members' => $teamMembers, // full rows
                ]);
            } else if ($user->user_category === 'Super Admin' || $user->user_category === 'Admin') {
                $teamMembers = DB::table('users')
                    ->whereNotNull('user_category')
                    ->get();

                // Optional: put in session
                session([
                    'team_names'   => $user->user_category,
                    'team_members' => $teamMembers, // full rows
                ]);
            }

            session([
                'employee_name' => $user->employee_name,
                'employee_code' => $user->employee_code,
                'session_id' => $user->id,
                'profile_picture' => $user->profile_picture,
                'job_title_designation' => $user->job_title_designation,
                'user_category' => $user->user_category,
                'zone' => $user->zone,
                'branch' => $user->branch,
                'firebase_token' => $user->firebase_token ?? '',
                'mobile_no_runo' => $user->mobile_no_runo ?? '',
                'session_team_name' => $user->team_name
            ]);


            // Get privilege data
            $lead_sources = DB::table('user_lead_soureces')
                ->where('employee', $userCode)
                ->first();

            session(['session_lead_source' => $lead_sources]);

            // Get privilege data
            $privilege = DB::table('grant_privileges')
                ->where('pri_group_name', $user->user_category)
                ->first();

            if ($privilege) {
                $menuIds = explode(',', $privilege->menubar_items);
                $buttonIds = explode(',', $privilege->action_buttons);

                // Get sidebar menus
                $sidebarMenus = DB::table('sidebar_menus')
                    ->whereIn('id', $menuIds)
                    ->get();

                $menuStructure = [];
                foreach ($sidebarMenus as $menu) {
                    $category = $menu->categories;
                    if (!isset($menuStructure[$category])) {
                        $menuStructure[$category] = [
                            'icon' => $menu->icons,
                            'items' => []
                        ];
                    }
                    $menuStructure[$category]['items'][] = [
                        'name' => $menu->name,
                        'url' => $menu->url
                    ];
                }

                session(['sidebar_menu' => $menuStructure]);

                // Get action buttons
                $actionButtons = DB::table('action_buttons')
                    ->whereIn('id', $buttonIds)
                    ->get();

                $buttonStructure = [];
                foreach ($actionButtons as $btn) {
                    $category = $btn->categories;
                    if (!isset($buttonStructure[$category])) {
                        $buttonStructure[$category] = [
                            'items' => []
                        ];
                    }
                    $buttonStructure[$category]['items'][] = [
                        'name' => $btn->name,
                        'class' => $btn->class,
                        'icon' => $btn->icon,
                    ];
                }

                session(['action_buttons' => $buttonStructure]);
            }

            // Get team information
            if (!empty($user->team_name)) {
                $team = DB::table('teams')
                    ->where('team_name', $user->team_name)
                    ->first();

                if ($team) {
                    session([
                        'session_team_leader' => $team->team_leader,
                        'session_group_name' => $team->group_name
                    ]);

                    // Get group information
                    if (!empty($team->group_name)) {
                        $group = DB::table('groups')
                            ->where('group_name', $team->group_name)
                            ->first();

                        if ($group) {
                            session(['session_group_leader' => $group->group_leader]);
                        }
                    }
                }
            }

            // Log session
            Log::debug('User logged in', session()->all());
            $this->logSession($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'redirect' => route('dashboard') // Add redirect URL
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    private function logSession($user)
    {
        $currentTime = Carbon::now('Asia/Kolkata');
        $deviceId = $this->getClientIP();

        try {
            // Insert into session_logs
            DB::table('session_logs')->insert([
                'session_id' => $user->id,
                'employee_code' => $user->employee_code,
                'session_name' => $user->employee_name,
                'login_date' => $currentTime,
                'access_from' => 'WebApp',
                'device_id' => $deviceId
            ]);

            // Update or insert into session_details
            $existingSession = DB::table('session_details')
                ->where('employee_code', $user->employee_code)
                ->first();

            if ($existingSession) {
                DB::table('session_details')
                    ->where('employee_code', $user->employee_code)
                    ->update([
                        'login_date' => $currentTime,
                        'access_from' => 'WebApp'
                    ]);
            } else {
                DB::table('session_details')->insert([
                    'session_id' => $user->id,
                    'employee_code' => $user->employee_code,
                    'session_name' => $user->employee_name,
                    'login_date' => $currentTime,
                    'access_from' => 'WebApp',
                    'device_id' => $deviceId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Session logging error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getClientIP()
    {
        $ip = request()->ip();

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $ip;
        }

        // Fallback to other methods if needed
        foreach (
            [
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            ] as $key
        ) {
            if ($ip = getenv($key)) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return 'UNKNOWN';
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
