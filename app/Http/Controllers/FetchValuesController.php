<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use app\Models\User;
use App\Models\SessionDetail;

class FetchValuesController extends Controller
{
    /**
     * Return distinct values for a column in registered_leads, with role-based filters.
     * Mirrors the old PHP script behavior.
     */
    public function distinctColumnValues(Request $request)
    {
        $column = trim((string) $request->input('columnName', ''));
        $table = trim((string) $request->input('tableName', ''));

        if ($column === '') {
            return response()->json(['error' => 'columnName parameter is missing or empty'], 422);
        }

        // Whitelist columns to avoid SQL injection via column name.
        $allowedColumns = [
            'lead_source',
            'branch',
            'zone',
            'lead_owner',
            'registered_name',
            'registered_email',
            'registered_mobile',
            'state',
            'city',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_adgroup',
            'utm_term',
            'level_applying_for',
            'course',
            'lead_stage',
            'lead_sub_stage',
            'lead_status',
            'widget_name',
            'lead_origin',
            'user_registration_date',
            'last_lead_activity_date',
            'last_enquirer_activity_date',
            'enquirer_activity_source',
        ];

        if (!in_array($column, $allowedColumns, true)) {
            return response()->json(['error' => 'Invalid columnName'], 422);
        }

        // Session / user context (fallback to session if not using Auth)
        $employeeCode = Auth::user()->employee_code ?? session('employee_code');
        $jobTitle = Auth::user()->job_title_designation ?? session('job_title_designation');
        $zone = Auth::user()->zone ?? session('zone');
        $branch = Auth::user()->branch ?? session('branch');

        // Special cases for lead_source based on employee_code (exactly like legacy)
        if ($column === 'lead_source') {
            if ($employeeCode === 'leadbazar_team') {
                return response()->json(['Google ADS HIT', 'Facebook HIT']);
            } elseif ($employeeCode === 'logic_loop_user') {
                return response()->json(['Google ADS LogicLoop', 'Facebook LL', 'GAds LL Call', 'LogicLoop INT']);
            } elseif ($employeeCode === 'college_dunia_team') {
                return response()->json(['College Dunia']);
            } elseif ($employeeCode === 'kollege_apply_team') {
                return response()->json(['Kollege Apply']);
            }
        }

        // Build base query
        $q = DB::table($table);

        // Role-based scoping (same logic as legacy)
        if ($jobTitle === 'Zonal Head' && in_array($column, ['branch', 'zone', 'lead_owner'], true) && $zone) {
            $q->where('zone', $zone);
        } elseif ($jobTitle === 'Branch Manager' && in_array($column, ['branch', 'zone', 'lead_owner'], true) && $branch) {
            $q->where('branch', $branch);
        }

        // Fetch distinct values
        try {
            $values = $q->distinct()
                ->orderBy($column)
                ->pluck($column)
                ->filter(function ($v) {
                    // Keep empty strings if they exist; just drop nulls
                    return !is_null($v);
                })
                ->values()
                ->all();

            return response()->json($values);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchAllUsers()
    {
        $users = User::all()->map(function ($user) {
            $session = SessionDetail::where('employee_code', $user->employee_code)
                ->latest('login_date')->first();

            $user->status = $session->status ?? 'Inactive';
            $user->login_date = $session->login_date ?? '-';

            $user->enable_calling = $user->enable_calling ?? 0;
            $user->working_status = $user->working_status ?? 1;

            return $user;
        });

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'users' => $users,
        ]);
    }
}
