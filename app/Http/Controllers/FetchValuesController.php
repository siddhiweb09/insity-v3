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
        $raw       = (string) $request->input('date_range', '');
        $table     = (string) $request->input('tableName', '');
        $category  = (string) $request->input('category', '');
        $dateCol   = (string) $request->input('date_source', 'created_at');

        // --- Validate table exists (no hardcoded list) ---
        if ($table === '' || !Schema::hasTable($table)) {
            return response()->json(['ok' => false, 'error' => 'Invalid table'], 422);
        }

        // --- Validate date column exists; fallback to created_at if available ---
        if (!Schema::hasColumn($table, $dateCol)) {
            if (Schema::hasColumn($table, 'created_at')) {
                $dateCol = 'created_at';
            } else {
                return response()->json(['ok' => false, 'error' => 'Invalid date column'], 422);
            }
        }

        // --- Parse date range (inclusive) ---
        if (preg_match('/^\d{4}-\d{2}-\d{2}\*\d{4}-\d{2}-\d{2}$/', $raw)) {
            [$fromDate, $toDate] = explode('*', $raw, 2);
        } else {
            $fromDate = Carbon::today()->subDays(7)->toDateString();
            $toDate   = Carbon::today()->toDateString();
        }
        if (Carbon::parse($fromDate)->gt(Carbon::parse($toDate))) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // --- Category → stage (your existing map) ---
        $stage = $category ? (self::STAGE_MAP[$category] ?? null) : null;
        if ($category && !$stage) {
            return response()->json(['ok' => false, 'error' => 'Invalid category'], 404);
        }

        // --- Session info & consistent owner format (use NAME*CODE to match team list) ---
        $employeeCode  = (string) session('employee_code');
        $employeeName  = (string) session('employee_name');
        $userCategory  = (string) session('user_category', '');
        $fmtOwner = fn(string $code, string $name) => "{$name}*{$code}";
        $selfOwner = $fmtOwner($employeeCode, $employeeName);

        // --- Base query ---
        $q = DB::table($table);

        // Inclusive date filter (works for datetime columns)
        $q->whereDate($dateCol, '>=', $fromDate)
            ->whereDate($dateCol, '<=', $toDate);

        // --- Role-based restriction ---
        if (!in_array($userCategory, ['Super Admin', 'Admin'], true)) {
            if (in_array($userCategory, ['Group Leader', 'Team Leader'], true)) {
                $leadOwners = [];
                foreach ((array) session('team_members', []) as $m) {
                    $leadOwners[] = $fmtOwner((string)($m['employee_code'] ?? ''), (string)($m['employee_name'] ?? ''));
                }
                // include self too
                $leadOwners[] = $selfOwner;
                $leadOwners = array_values(array_filter($leadOwners));
                if ($leadOwners) {
                    $q->whereIn('lead_owner', $leadOwners);
                } else {
                    $q->whereRaw('1=0'); // no team -> return none
                }
            } else {
                $q->where('lead_owner', $selfOwner);
            }
        }

        // --- Stage filter ---
        if ($stage) {
            $q->where('lead_stage', $stage);
        }

        // --- Dynamic filters (0..19) driven by request, schema-checked per column ---
        // Allowed operators kept minimal for safety, but taken from request (no hardcoded columns).
        $allowedOps = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN'];

        for ($i = 0; $i < 20; $i++) {
            // var_dump($i);
            $suf    = $i === 0 ? '' : (string)$i;
            // var_dump($request->input('filterTitle'  . $suf));

            $column = $request->input('filterTitle'  . $suf);
            $opIn   = strtoupper((string) $request->input('filterSearch' . $suf));
            $value  =              $request->input('filterValue'  . $suf);

            // var_dump($column);
            // var_dump($opIn);
            // var_dump($value);

            // Normalize/guard operator (default to '=' if invalid)
            $op = in_array($opIn, $allowedOps, true) ? $opIn : '=';

            // Array values
            if (is_array($value)) {
                $vals = array_values(array_filter($value, fn($v) => $v !== '' && $v !== null));
                if (!$vals) continue;

                if ($op === 'IN') {
                    $q->whereIn($column, $vals);
                } elseif ($op === 'NOT IN') {
                    $q->whereNotIn($column, $vals);
                } elseif ($op === 'LIKE') {
                    $q->where(function ($sub) use ($column, $vals) {
                        foreach ($vals as $v) {
                            $sub->orWhere($column, 'LIKE', '%' . str_replace(['%', '_'], ['\%', '\_'], (string)$v) . '%');
                        }
                    });
                } elseif ($op === 'NOT LIKE') {
                    // Use AND for NOT LIKE list (correct logic)
                    $q->where(function ($sub) use ($column, $vals) {
                        foreach ($vals as $v) {
                            $sub->where($column, 'NOT LIKE', '%' . str_replace(['%', '_'], ['\%', '\_'], (string)$v) . '%');
                        }
                    });
                } else {
                    // For array + non-list operator, skip
                    continue;
                }
                continue;
            }

            // Scalar values
            if ($op === 'LIKE' || $op === 'NOT LIKE') {
                $q->where($column, $op, '%' . str_replace(['%', '_'], ['\%', '\_'], (string)$value) . '%');
            } elseif ($op === 'BETWEEN') {
                $a = $request->input('filterValueFirst'  . $suf);
                $b = $request->input('filterValueSecond' . $suf);
                if ($a !== null && $b !== null) {
                    // normalize numeric order
                    if (is_numeric($a) && is_numeric($b) && $a > $b) [$a, $b] = [$b, $a];
                    $q->whereBetween($column, [$a, $b]);
                }
            } else {
                $q->where($column, $op, $value);
            }
        }

        // var_dump($q->toSql());
        // var_dump($q->getBindings());

        dd([
            'sql' => $q->toSql(),
            'bindings' => $q->getBindings(),
        ]);


        // --- Order & fetch (optional limit via request, with a hard cap) ---
        $leads = $q->orderByDesc($dateCol)->get();

        // --- Store (as you had) + return JSON ---
        session()->forget([
            'table',
            'leads',
            'category',
            'stageName',
            'categories',
        ]);
        session([
            'table'      => $table,
            'leads'      => $leads,
            'category'   => $category ?: 'all',
            'stageName'  => $stage ?: 'All',
            'categories' => array_keys(self::STAGE_MAP),
        ]);

        return response()->json([
            'ok'    => true,
            'count' => $leads->count(),
            'from'  => $fromDate,
            'to'    => $toDate,
        ]);
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
