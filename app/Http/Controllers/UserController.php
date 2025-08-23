<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            'id' => 'required|integer|exists:groups,id',
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

        [$id, $name, $zone, $leader] = explode('*', $decoded);

        // Fetch teams from the database ordered by updated_at
        $teams = DB::table('teams')
            ->orderBy('updated_at', 'DESC')
            ->get();
        foreach ($teams as $team) {
            dd($team->group_name);
        }

        // Pass the teams to the view using compact
        return view('user.team_mapping', compact('id', 'name', 'zone', 'leader', 'teams'));
    }
}
