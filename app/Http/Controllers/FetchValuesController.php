<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredLead;
use App\Models\ActiveLeadSource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use app\Models\User;
use App\Models\SessionDetail;
use Illuminate\Support\Facades\Schema;
use App\Models\ActionButton;
use App\Models\SidebarMenu;
use App\Models\GrantPrivilege;


class FetchValuesController extends Controller
{
    private const STAGE_MAP = [
        'untouched' => 'Untouched',
        'hot' => 'Hot',
        'warm' => 'Warm',
        'cold' => 'Cold',
        'inquiry' => 'Inquiry',
        'admission-in-process' => 'Admission In Process',
        'admission-done' => 'Admission Done',
        'scrap' => 'Scrap',
        'non-qualified' => 'Non Qualified',
        'non-contactable' => 'Non-Contactable',
        'follow-up' => 'Follow-Up',
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
        $counselors = User::select('employee_code', 'employee_name')
            ->get()
            ->map(function ($user) {
                return $user->employee_code . '*' . $user->employee_name;
            });

        return response()->json([
            'counselors' => $counselors
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
        $dateCol = (string) $request->input('date_source', 'created_at');

        // --- Validate table exists ---
        if ($table === '' || !Schema::hasTable($table)) {
            return response()->json(['ok' => false, 'error' => 'Invalid table'], 422);
        }

        // --- Validate date column exists ---
        if (!Schema::hasColumn($table, $dateCol)) {
            if (Schema::hasColumn($table, 'created_at')) {
                $dateCol = 'created_at';
            } else {
                return response()->json(['ok' => false, 'error' => 'Invalid date column'], 422);
            }
        }

        // --- Parse date range ---
        if (preg_match('/^\d{4}-\d{2}-\d{2}\*\d{4}-\d{2}-\d{2}$/', $raw)) {
            [$fromDate, $toDate] = explode('*', $raw, 2);
        } else {
            $fromDate = Carbon::today()->subDays(7)->toDateString();
            $toDate = Carbon::today()->toDateString();
        }
        if (Carbon::parse($fromDate)->gt(Carbon::parse($toDate))) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // --- Category → stage ---
        $stage = $category ? (self::STAGE_MAP[$category] ?? null) : null;
        if ($category && !$stage) {
            return response()->json(['ok' => false, 'error' => 'Invalid category'], 404);
        }

        // --- Session info ---
        $employeeCode  = (string) session('employee_code');
        $employeeName  = (string) session('employee_name');
        $userCategory  = (string) session('user_category', '');
        $fmtOwner = fn(string $code, string $name) => "{$name}*{$code}";
        $selfOwner = $fmtOwner($employeeCode, $employeeName);

        // --- Base query ---
        $q = DB::table($table);

        $q->whereDate($dateCol, '>=', $fromDate)
            ->whereDate($dateCol, '<=', $toDate);

        // --- Role-based restriction ---
        if (!in_array($userCategory, ['Super Admin', 'Admin'], true)) {
            if (in_array($userCategory, ['Group Leader', 'Team Leader'], true)) {
                $leadOwners = [];
                foreach ((array) session('team_members', []) as $m) {
                    $leadOwners[] = $fmtOwner(
                        (string)($m['employee_code'] ?? ''),
                        (string)($m['employee_name'] ?? '')
                    );
                }
                $leadOwners[] = $selfOwner;
                $leadOwners = array_values(array_filter($leadOwners));
                if ($leadOwners) {
                    $q->whereIn('lead_owner', $leadOwners);
                } else {
                    $q->whereRaw('1=0');
                }
            } else {
                $q->where('lead_owner', $selfOwner);
            }
        }

        // --- Stage filter ---
        if ($stage) {
            $q->where('lead_stage', $stage);
        }

        // --- Dynamic filters (0..19) ---
        $allowedOps = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN'];

        for ($i = 0; $i < 20; $i++) {
            $suf = (string)$i;

            $column = $request->input('filterTitle' . $suf);
            $opIn   = strtoupper((string) $request->input('filterSearch' . $suf));

            if (!$column) continue;

            $op = in_array($opIn, $allowedOps, true) ? $opIn : '=';

            // --- Collect values ---
            $valueKey = 'filterValue' . $suf;
            $value = $request->input($valueKey);

            if (!is_array($value)) {
                $queryVals = $request->query($valueKey);
                if (is_array($queryVals)) {
                    $value = $queryVals;
                } elseif ($value !== null && $value !== '') {
                    $value = [$value];
                } else {
                    $value = [];
                }
            }

            $vals = array_values(array_filter($value, fn($v) => $v !== '' && $v !== null));

            // --- Apply filters ---
            if ($op === 'IN') {
                if ($vals) $q->whereIn($column, $vals);
            } elseif ($op === 'NOT IN') {
                if ($vals) $q->whereNotIn($column, $vals);
            } elseif ($op === 'LIKE') {
                if ($vals) {
                    $q->where(function ($sub) use ($column, $vals) {
                        foreach ($vals as $v) {
                            $sub->orWhere($column, 'LIKE', '%' . str_replace(['%', '_'], ['\%', '\_'], (string) $v) . '%');
                        }
                    });
                }
            } elseif ($op === 'NOT LIKE') {
                if ($vals) {
                    $q->where(function ($sub) use ($column, $vals) {
                        foreach ($vals as $v) {
                            $sub->where($column, 'NOT LIKE', '%' . str_replace(['%', '_'], ['\%', '\_'], (string) $v) . '%');
                        }
                    });
                }
            } elseif ($op === 'BETWEEN') {
                $a = $request->input('filterValueFirst');
                $b = $request->input('filterValueSecond');

                if ($a !== null && $b !== null && $a !== '' && $b !== '') {
                    if (is_numeric($a) && is_numeric($b) && $a > $b) {
                        [$a, $b] = [$b, $a]; // normalize order
                    }
                    $q->whereBetween($column, [$a, $b]);
                }
            } elseif (in_array($op, ['=', '!=', '>', '<', '>=', '<='], true)) {
                if ($vals) {
                    $q->where(function ($sub) use ($column, $op, $vals) {
                        foreach ($vals as $v) {
                            $sub->orWhere($column, $op, $v);
                        }
                    });
                }
            } else {
                if (count($vals) === 1) {
                    $q->where($column, '=', $vals[0]);
                }
            }
        }

        // Debug
        // var_dump([
        //     'sql'      => $q->toSql(),
        //     'bindings' => $q->getBindings(),
        // ]);

        // --- Order & fetch ---
        $leads = $q->orderByDesc($dateCol)->get();
        // dd($leads);

        session()->forget([
            'table',
            'leads',
            'category',
            'stageName',
            'categories',
        ]);
        session([
            'table' => $table,
            'leads' => $leads,
            'category' => $category ?: 'all',
            'stageName' => $stage ?: 'All',
            'categories' => array_keys(self::STAGE_MAP),
        ]);

        return response()->json([
            'ok' => true,
            'count' => $leads->count(),
            'from' => $fromDate,
            'to' => $toDate,
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

    public function getDesignations($department)
    {
        // Fetch distinct designations for the selected department
        $designations = User::where('department', $department)
            ->whereNotNull('job_title_designation')
            ->distinct()
            ->pluck('job_title_designation');

        return response()->json($designations);
    }

    public function getBranches($zone)
    {
        // Fetch distinct designations for the selected department
        $branches = User::where('zone', $zone)
            ->whereNotNull('branch')
            ->distinct()
            ->pluck('branch');

        return response()->json($branches);
    }
    public function fetchPrivilegesData()
    {
        try {
            // Fetch all grant privileges ordered by latest
            $privileges = GrantPrivilege::orderBy('created_at', 'desc')->get();

            $response = [
                'success' => true,
                'data' => []
            ];

            foreach ($privileges as $priv) {
                $privilegeData = [
                    'id' => $priv->id,
                    'pri_group_name' => $priv->pri_group_name,
                    'created_at' => $priv->created_at,
                    'action_buttons' => [],
                    'menubar_items' => []
                ];

                // Process action buttons
                if (!empty($priv->action_buttons)) {
                    $actionButtonIds = explode(',', $priv->action_buttons);
                    $actionButtons = ActionButton::whereIn('id', $actionButtonIds)
                        ->select('id', 'name')
                        ->get();

                    foreach ($actionButtons as $btn) {
                        $privilegeData['action_buttons'][] = [
                            'id' => $btn->id,
                            'name' => $btn->name
                        ];
                    }
                }

                // Process menu bar items
                if (!empty($priv->menubar_items)) {
                    $menuItemIds = explode(',', $priv->menubar_items);
                    $menuItems = SidebarMenu::whereIn('id', $menuItemIds)
                        ->select('id', 'name')
                        ->get();

                    foreach ($menuItems as $menu) {
                        $privilegeData['menubar_items'][] = [
                            'id' => $menu->id,
                            'name' => $menu->name
                        ];
                    }
                }

                $response['data'][] = $privilegeData;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function fetchSidebarMenusActionButtons()
    {
        try {
            $allSidebarMenus = SidebarMenu::orderBy('created_at', 'asc')->get();
            $allActionButtons = ActionButton::orderBy('created_at', 'asc')->get();

            return response()->json([
                'success' => true,
                'sidebar_menus' => $allSidebarMenus,
                'action_buttons' => $allActionButtons
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchActiveLeadSources()
    {
        try {
            // Fetch active lead sources
            $leadSources = DB::table('lead_source')
                ->where('status', 'Active')
                ->pluck('sources'); // only get the "sources" column

            return response()->json([
                'sources' => $leadSources
            ]);
        } catch (\Exception $e) {
            \Log::error("Database query failed: " . $e->getMessage());

            return response()->json([
                'error' => 'Database query failed'
            ], 500);
        }
    }

    public function fetchLeadStages()
    {
        try {
            // Fetch active lead sources
            $lead_stages = DB::table('lead_stages')->distinct()->orderBy('lead_stage', 'asc')->pluck('lead_stage');
            return response()->json([
                'lead_stages' => $lead_stages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database query failed'
            ], 500);
        }
    }
    public function fetchLeadSubStages(Request $request)
    {
        try {
            $lead_stage = $request->input('lead_stage');

            $lead_sub_stages = DB::table('lead_stages')
                ->where('lead_stage', $lead_stage)
                ->whereNotNull('lead_sub_stage')       // optional: skip NULLs
                ->select('lead_sub_stage')
                ->distinct()
                ->orderBy('lead_sub_stage', 'asc')
                ->pluck('lead_sub_stage')
                ->values();                             // reindex

            return response()->json([
                'lead_sub_stages' => $lead_sub_stages
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
