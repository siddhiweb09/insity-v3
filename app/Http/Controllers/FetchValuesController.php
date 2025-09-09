<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredLead;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use app\Models\User;
use App\Models\SessionDetail;
use Illuminate\Support\Facades\Schema;


class FetchValuesController extends Controller
{
    private const STAGE_MAP = [
        'untouched'            => 'Untouched',
        'hot'                  => 'Hot',
        'warm'                 => 'Warm',
        'cold'                 => 'Cold',
        'inquiry'              => 'Inquiry',
        'admission-in-process' => 'Admission In Process',
        'admission-done'       => 'Admission Done',
        'scrap'                => 'Scrap',
        'non-qualified'        => 'Non Qualified',
        'non-contactable'      => 'Non-Contactable',
        'follow-up'            => 'Follow-Up',
    ];

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
    public function distinctTitleValues(Request $request)
    {
        $table = trim((string) $request->input('tableName', ''));

        try {
            if (!Schema::hasTable($table)) {
                return response()->json(['error' => "Table '$table' does not exist."], 404);
            }

            // Get all column names
            $columns = Schema::getColumnListing($table);

            return response()->json($columns);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function filteredValues(Request $request)
    {
        $raw = (string) $request->input('date_range', '');
        $table = (string) $request->input('tableName', '');
        $category = (string) $request->input('category', '');

        if (preg_match('/^\d{4}-\d{2}-\d{2}\*\d{4}-\d{2}-\d{2}$/', $raw)) {
            [$fromDate, $toDate] = explode('*', $raw, 2);
        } else {
            $fromDate = Carbon::today()->subDays(7)->toDateString();
            $toDate   = Carbon::today()->toDateString();
        }

        if (Carbon::parse($fromDate)->gt(Carbon::parse($toDate))) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $dateSource = $request->input('date_source', 'created_at');

        $stage = $category ? (self::STAGE_MAP[$category] ?? null) : null;
        if ($category && !$stage) {
            abort(404);
        }

        // dd($stage);

        $employeeCode = session('employee_code');
        $employeeName = session('employee_name');
        $user_category = session('user_category');

        $q = DB::table($table);

        // Date filter
        $q->whereBetween($dateSource, [$fromDate, $toDate]);

        // User-based filter
        if (!in_array($user_category, ['Super Admin', 'Admin'])) {
            if (in_array($user_category, ['Group Leader', 'Team Leader'])) {
                $leadOwners = [];
                foreach (session('team_members', []) as $member) {
                    $leadOwners[] = $member['employee_name'] . "*" . $member['employee_code'];
                }
                $q->whereIn('lead_owner', $leadOwners);
            } else {
                $q->where('lead_owner', $employeeCode . "*" . $employeeName);
            }
        }

        // Stage filter
        if ($stage) {
            $q->where('lead_stage', $stage);
        }

        // Dynamic filters (up to 20)
        for ($i = 0; $i < 20; $i++) {
            $suffix = $i === 0 ? '' : $i;
            $title  = $request->input('filterTitle' . $suffix);
            $search = $request->input('filterSearch' . $suffix);
            $value  = $request->input('filterValue' . $suffix);

            var_dump($title);

            if ($title && $search && $value !== null) {

                if (is_array($value)) {
                    if ($search === 'IN') {
                        $q->whereIn($title, $value);
                    } elseif ($search === 'NOT IN') {
                        $q->whereNotIn($title, $value);
                    } elseif (in_array($search, ['LIKE', 'NOT LIKE'])) {
                        $q->where(function ($sub) use ($title, $value, $search) {
                            foreach ($value as $v) {
                                $sub->orWhere($title, $search, "%$v%");
                            }
                        });
                    }
                } else {
                    if (in_array($search, ['LIKE', 'NOT LIKE'])) {
                        $q->where($title, $search, "%$value%");
                    } elseif ($search === 'BETWEEN') {
                        $val1 = $request->input('filterValueFirst' . $suffix);
                        $val2 = $request->input('filterValueSecond' . $suffix);
                        if ($val1 && $val2) {
                            $q->whereBetween($title, [$val1, $val2]);
                        }
                    } else {
                        // default to '=' if operator is invalid
                        $q->where($title, $search ?? '=', $value);
                    }
                }
            }
        }

        $leads = $q->latest($dateSource)->get();

        session([
            'table'      => $table,
            'leads'      => $leads,
            'category'   => $category ?? 'all',
            'stageName'  => $stage ?? 'All',
            'categories' => array_keys(self::STAGE_MAP),
        ]);

        // Return as JSON instead of storing in session
        //return response()->json(['ok' => true]);
    }

    public function clearFilter(Request $request)
    {
        session()->forget([
            'table',
            'leads',
            'category',
            'stageName',
            'categories',
        ]);

        // Optionally, return a response
        return response()->json(['ok' => true, 'message' => 'Filters cleared successfully.']);
    }
}
