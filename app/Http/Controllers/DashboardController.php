<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function leadDashboard()
    {
        return view('dashboard.leadDashboard');
    }

    public function leadStats(Request $request)
    {
        $validated = $request->validate([
            'dateRange'   => ['required', 'string', function ($attr, $value, $fail) {
                $parts = explode('*', urldecode($value));
                if (count($parts) !== 2) return $fail('dateRange must be "YYYY-MM-DD*YYYY-MM-DD".');
                foreach ($parts as $p) {
                    $d = \DateTime::createFromFormat('Y-m-d', $p);
                    if (!$d || $d->format('Y-m-d') !== $p) return $fail('Invalid date format in dateRange.');
                }
            }],
            'requested_for' => ['nullable', 'string'],
            'date_source'   => ['nullable', 'string'],
        ]);

        [$startDate, $endDate] = explode('*', urldecode($request->string('dateRange')));
        $dateSource = $request->string('date_source')->value() ?: 'lead_assignment_date';
        $requestedFor = urldecode($request->string('requested_for')->value() ?? '');

        // ---- Current user/session context ----
        // If you truly rely on session fields set elsewhere, keep using session():
        $session = session();
        $employeeCode = $session->get('employee_code'); // matches your original script
        $employeeName = $session->get('employee_name');
        $user_category       = $session->get('user_category');
        $designation  = $session->get('job_title_designation');

        // Fallbacks from Auth if available
        if (!$employeeCode && Auth::check()) {
            $employeeCode = Auth::user()->employee_code ?? null;
        }
        if (!$employeeName && Auth::check()) {
            $employeeName = Auth::user()->employee_name ?? null;
        }

        // ---- Build base query safely ----
        $columns = [
            DB::raw("DATE($dateSource) as date"),
            'lead_source',
            'branch',
            'zone',
            'lead_owner',
            'lead_stage',
            'application_submitted',
            'lead_status',
            'lead_origin',
            'state'
        ];

        $query = DB::table('registered_leads')
            ->select($columns)
            ->whereBetween(DB::raw("DATE($dateSource)"), [$startDate, $endDate]);

        $leadOwners = [];

        if ($user_category === "Super Admin" || $user_category === "Admin") {
            // no restrictions
        } elseif ($user_category === "Group Leader" || $user_category === "Team Leader") {
            foreach (session('team_members', []) as $member) {
                $leadOwners[] = $member['employee_name'] . "*" . $member['employee_code'];
            }
            $query->whereIn('lead_owner', $leadOwners);
        } else {
            $leadOwner = $employeeCode . "*" . $employeeName;
            $query->where('lead_owner', $leadOwner);
        }

        if ($requestedFor !== "" && $requestedFor !== "All") {
            $query->where('lead_source', $requestedFor);
        }

        $query->orderBy('date', 'ASC');

        // ---- Fetch rows ----
        $rows = $query->get(); // collection of stdClass

        // ---- Buckets / group logic (ported) ----
        $stages = ["Untouched", "Hot", "Warm", "Cold", "Inquiry", "Admission In Process", "Admission Done", "Scrap", "Non Qualified", "Non-Contactable", "Follow-Up", "Total"];

        $data = $rows->map(function ($r) {
            return [
                'date'                  => $r->date,
                'lead_source'           => $r->lead_source,
                'branch'                => $r->branch,
                'zone'                  => $r->zone,
                'lead_owner'            => $r->lead_owner,
                'lead_stage'            => $r->lead_stage,
                'application_submitted' => $r->application_submitted,
                'lead_status'           => $r->lead_status,
                'lead_origin'           => $r->lead_origin,
                'state'                 => $r->state,
            ];
        })->all();

        // Helper to init stage array
        $initStageArray = function () use ($stages) {
            return array_fill_keys($stages, 0);
        };

        $groupedDataSourceLS = [];
        $groupedDataByBranch = [];
        $groupedDataByCounsellor = [];
        $groupedDataBySourceBranchCounsellor = [];
        $leadStageCount = $initStageArray();

        // 1) By lead_source (source_ls_count)
        foreach ($data as $entry) {
            $source = $entry['lead_source'];
            $leadStage = $entry['lead_stage'];
            $app = $entry['application_submitted'];

            if (!isset($groupedDataSourceLS[$source])) {
                $groupedDataSourceLS[$source] = $initStageArray();
            }

            if ($leadStage === "Admission Done") {
                if ($app === "NO") {
                    $groupedDataSourceLS[$source]["Admission In Process"]++;
                } elseif ($app === "YES") {
                    $groupedDataSourceLS[$source]["Admission Done"]++;
                }
            } elseif (in_array($leadStage, $stages)) {
                $groupedDataSourceLS[$source][$leadStage]++;
            }
        }

        // 2) By branch -> lead_source (source_branch_count)
        foreach ($data as $entry) {
            $branch = $entry['branch'];
            $source = $entry['lead_source'];
            $leadStage = $entry['lead_stage'];
            $app = $entry['application_submitted'];

            $groupedDataByBranch[$branch] ??= [];
            $groupedDataByBranch[$branch][$source] ??= $initStageArray();

            if ($leadStage === "Admission Done") {
                if ($app === "NO") {
                    $groupedDataByBranch[$branch][$source]["Admission In Process"]++;
                } elseif ($app === "YES") {
                    $groupedDataByBranch[$branch][$source]["Admission Done"]++;
                }
            } elseif (in_array($leadStage, $stages)) {
                $groupedDataByBranch[$branch][$source][$leadStage]++;
            }
        }

        // 3) By branch -> lead_owner (lead_owner_branch_count)
        foreach ($data as $entry) {
            $branch = $entry['branch'];
            $owner  = $entry['lead_owner'];
            $leadStage = $entry['lead_stage'];
            $app = $entry['application_submitted'];

            $groupedDataByCounsellor[$branch] ??= [];
            $groupedDataByCounsellor[$branch][$owner] ??= $initStageArray();

            if ($leadStage === "Admission Done") {
                if ($app === "NO") {
                    $groupedDataByCounsellor[$branch][$owner]["Admission In Process"]++;
                } elseif ($app === "YES") {
                    $groupedDataByCounsellor[$branch][$owner]["Admission Done"]++;
                }
            } elseif (in_array($leadStage, $stages)) {
                $groupedDataByCounsellor[$branch][$owner][$leadStage]++;
            }
        }

        // 4) By branch -> source -> lead_owner (source_lead_owner_branch_count)
        foreach ($data as $entry) {
            $branch = $entry['branch'];
            $source = $entry['lead_source'];
            $owner  = $entry['lead_owner'];
            $leadStage = $entry['lead_stage'];
            $app = $entry['application_submitted'];

            $groupedDataBySourceBranchCounsellor[$branch] ??= [];
            $groupedDataBySourceBranchCounsellor[$branch][$source] ??= [];
            $groupedDataBySourceBranchCounsellor[$branch][$source][$owner] ??= $initStageArray();

            if ($leadStage === "Admission Done") {
                if ($app === "NO") {
                    $groupedDataBySourceBranchCounsellor[$branch][$source][$owner]["Admission In Process"]++;
                } elseif ($app === "YES") {
                    $groupedDataBySourceBranchCounsellor[$branch][$source][$owner]["Admission Done"]++;
                }
            } elseif (in_array($leadStage, $stages)) {
                $groupedDataBySourceBranchCounsellor[$branch][$source][$owner][$leadStage]++;
            }
        }

        // 5) Overall leadStageCount (with Admission In Process rule)
        foreach ($data as $entry) {
            $leadStage = $entry['lead_stage'];
            $app = $entry['application_submitted'];

            if ($leadStage === "Admission Done") {
                if ($app === "NO") {
                    $leadStageCount["Admission In Process"]++;
                } elseif ($app === "YES") {
                    $leadStageCount["Admission Done"]++;
                }
            } elseif (in_array($leadStage, $stages)) {
                $leadStageCount[$leadStage]++;
            }
        }
        $leadStageCount["Total"] = count($data);

        // 6) Chart counts
        $leadOriginCount = [];
        $leadStatusCount = [];
        $leadSourceCount = [];
        $leadDateCount   = [];
        $leadStateCount  = [];
        $leadBranchCount = [];
        $leadZoneCount   = [];

        foreach ($data as $entry) {
            $leadOriginCount[$entry['lead_origin']] = ($leadOriginCount[$entry['lead_origin']] ?? 0) + 1;
            $leadStatusCount[$entry['lead_status']] = ($leadStatusCount[$entry['lead_status']] ?? 0) + 1;
            $leadSourceCount[$entry['lead_source']] = ($leadSourceCount[$entry['lead_source']] ?? 0) + 1;
            $leadDateCount[$entry['date']]          = ($leadDateCount[$entry['date']] ?? 0) + 1;
            $leadStateCount[$entry['state']]        = ($leadStateCount[$entry['state']] ?? 0) + 1;
            $leadBranchCount[$entry['branch']]      = ($leadBranchCount[$entry['branch']] ?? 0) + 1;
            $leadZoneCount[$entry['zone']]          = ($leadZoneCount[$entry['zone']] ?? 0) + 1;
        }

        // Add totals into each grouped bucket
        foreach ($groupedDataSourceLS as $source => $stagesArr) {
            $groupedDataSourceLS[$source]['Total'] = array_sum($stagesArr);
        }

        foreach ($groupedDataByBranch as $branch => &$sources) {
            foreach ($sources as $source => &$stagesArr) {
                $stagesArr['Total'] = array_sum($stagesArr);
            }
        }

        foreach ($groupedDataByCounsellor as $branch => &$owners) {
            foreach ($owners as $owner => &$stagesArr) {
                $stagesArr['Total'] = array_sum($stagesArr);
            }
        }

        foreach ($groupedDataBySourceBranchCounsellor as $branch => &$sources) {
            foreach ($sources as $source => &$owners) {
                foreach ($owners as $owner => &$stagesArr) {
                    $stagesArr['Total'] = array_sum($stagesArr);
                }
            }
        }

        // ---- Response ----
        return response()->json([
            'lead_stage_count'                 => $leadStageCount,
            'source_ls_count'                  => $groupedDataSourceLS,
            'source_branch_count'              => $groupedDataByBranch,
            'lead_owner_branch_count'          => $groupedDataByCounsellor,
            'source_lead_owner_branch_count'   => $groupedDataBySourceBranchCounsellor,
            'lead_origin_count'                => $leadOriginCount,
            'lead_status_count'                => $leadStatusCount,
            'lead_source_count'                => $leadSourceCount,
            'lead_date_count'                  => $leadDateCount,
            'lead_state_count'                 => $leadStateCount,
            'lead_branch_count'                => $leadBranchCount,
            'lead_zone_count'                  => $leadZoneCount,
        ]);
    }

    public function adminDashboard()
    {
        return view('dashboard.adminDashboard');
    }

    public function adminStats(Request $request)
    {
        try {
            // Parse dateRange like "2024-06*2025-08"
            $dateRange = (string) $request->query('dateRange', '');
            [$startRaw, $endRaw] = array_pad(explode('*', urldecode($dateRange)), 2, null);

            if (!$startRaw || !$endRaw) {
                return response()->json(['error' => 'dateRange is required as YYYY-MM*YYYY-MM'], 422);
            }

            // Validate YYYY-MM and build month boundaries
            try {
                $startDate = Carbon::createFromFormat('Y-m', $startRaw)->startOfMonth();
                $endDate   = Carbon::createFromFormat('Y-m', $endRaw)->endOfMonth();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format. Use YYYY-MM*YYYY-MM'], 422);
            }

            // Query (keep columns aligned with original script)
            $rows = DB::table('registered_leads')
                ->select([
                    'lead_id',
                    'lead_source',
                    'branch',
                    'zone',
                    'state',
                    'lead_stage',
                    'application_submitted',
                    'lead_assignment_date',
                ])
                ->whereBetween(DB::raw('DATE(lead_assignment_date)'), [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ])
                ->orderBy('lead_assignment_date', 'desc')
                ->get();

            // Build monthly buckets
            $monthly = [];  // [ 'YYYY-MM' => [ ... ] ]

            foreach ($rows as $row) {
                $month      = Carbon::parse($row->lead_assignment_date)->format('Y-m');
                $monthName  = Carbon::parse($row->lead_assignment_date)->format('F');

                if (!isset($monthly[$month])) {
                    $monthly[$month] = [
                        'month'                   => $month,
                        'month_name'              => $monthName,
                        'total_leads'             => 0,
                        'source_leads'            => [], // [lead_source => count]
                        'branch_leads'            => [], // [branch => count]
                        'zone_leads'              => [], // [zone => count]
                        'state_leads'             => [], // [state => count]
                        'admission_source_leads'  => [], // [lead_source => count] (Admission Done + YES)
                    ];
                }

                // Totals
                $monthly[$month]['total_leads']++;

                // Per source / branch / zone / state
                $src    = $row->lead_source ?? 'N/A';
                $branch = $row->branch ?? 'N/A';
                $zone   = $row->zone ?? 'N/A';
                $state  = $row->state ?? 'N/A';

                $monthly[$month]['source_leads'][$src]    = ($monthly[$month]['source_leads'][$src] ?? 0) + 1;
                $monthly[$month]['branch_leads'][$branch] = ($monthly[$month]['branch_leads'][$branch] ?? 0) + 1;
                $monthly[$month]['zone_leads'][$zone]     = ($monthly[$month]['zone_leads'][$zone] ?? 0) + 1;
                $monthly[$month]['state_leads'][$state]   = ($monthly[$month]['state_leads'][$state] ?? 0) + 1;

                // Admission source count (Admission Done + application_submitted === 'YES')
                if (($row->lead_stage === 'Admission Done') && (strtoupper($row->application_submitted ?? '') === 'YES')) {
                    $monthly[$month]['admission_source_leads'][$src] =
                        ($monthly[$month]['admission_source_leads'][$src] ?? 0) + 1;
                }
            }

            // Prepare final output (sorted by month desc)
            $final = [];
            foreach ($monthly as $m => $data) {
                // Helpers to get top key & count from an assoc array
                $topKey = function (array $arr): string {
                    if (empty($arr)) return 'N/A';
                    $maxVal = max($arr);
                    // When multiple match max, keep first key like original
                    foreach ($arr as $k => $v) {
                        if ($v === $maxVal) return $k;
                    }
                    return 'N/A';
                };
                $topVal = function (array $arr): int {
                    return empty($arr) ? 0 : max($arr);
                };

                // Top performers
                $top_performers = [
                    'top_source'                        => $topKey($data['source_leads']),
                    'top_source_lead_count'             => $topVal($data['source_leads']),

                    // Note: original script implodes keys for top_admission_source when multiple match.
                    // We'll do the same.
                    'top_admission_source'              => !empty($data['admission_source_leads'])
                        ? implode(', ', array_keys($data['admission_source_leads'], max($data['admission_source_leads'])))
                        : 'N/A',
                    'top_admission_source_lead_count'   => $topVal($data['admission_source_leads']),

                    'top_branch'                        => $topKey($data['branch_leads']),
                    'top_branch_lead_count'             => $topVal($data['branch_leads']),

                    'top_zone'                          => $topKey($data['zone_leads']),
                    'top_zone_lead_count'               => $topVal($data['zone_leads']),

                    'top_state'                         => $topKey($data['state_leads']),
                    'top_state_lead_count'              => $topVal($data['state_leads']),
                ];

                // Convert source_leads to [{lead_source, total_leads}, ...]
                $source_leads_array = [];
                foreach ($data['source_leads'] as $src => $count) {
                    $source_leads_array[] = [
                        'lead_source' => $src,
                        'total_leads' => $count,
                    ];
                }

                $final[] = [
                    'month'         => $data['month'],
                    'month_name'    => $data['month_name'],
                    'total_leads'   => $data['total_leads'],
                    'source_leads'  => $source_leads_array,
                    'top_performers' => $top_performers,
                ];
            }

            // Sort by month desc (like original usort with strtotime)
            usort($final, function ($a, $b) {
                return strtotime($b['month']) <=> strtotime($a['month']);
            });

            return response()->json($final, 200, [], JSON_PRETTY_PRINT);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function collectionDashboard()
    {
        return view('dashboard.collectionDashboard');
    }

    public function collectionStats(Request $request)
    {
        $dateRange       = (string) $request->query('dateRange', '');
        $widgetNameValue = (string) $request->query('widgetNameValue', '');

        [$startRaw, $endRaw] = array_pad(explode('*', urldecode($dateRange)), 2, null);

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startRaw)->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m-d', $endRaw)->toDateString();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid date format'], 422);
        }

        // ---- Role-based filters (using session, same as original) ----
        $jobTitle = session('job_title_designation');
        $user_category   = session('user_category');
        $zone     = session('zone');
        $branch   = session('branch');
        $empCode  = session('employee_code');
        $empName  = session('employee_name');

        // ---- Base query (keep selected columns aligned with your script) ----
        $q = DB::table('finance_records')
            ->select([
                DB::raw('DATE(record_date) as record_date'),
                'zone',
                'branch',
                'counselor',
                'entity',
                'total_receipt',
                'total_settled',
                'total_pending',
                'pr_sales_amount',
                'sr_sales_amount',
                'admission_count',
                'isbmfc001_receipt',
                'isbmfc001_settled',
                'isbmfc001_pending',
                'isbmpdc002_receipt',
                'isbmpdc002_settled',
                'isbmpdc002_pending',
                'isbmpdcfc003_receipt',
                'isbmpdcfc003_settled',
                'isbmpdcfc003_pending',
                'isbmoc004_receipt',
                'isbmoc004_settled',
                'isbmoc004_pending',
                'isbmrc005_receipt',
                'isbmrc005_settled',
                'isbmrc005_pending',
                'isbmpc006_receipt',
                'isbmpc006_settled',
                'isbmpc006_pending',
            ])
            ->whereBetween(DB::raw('DATE(record_date)'), [$startDate, $endDate]);

        if (!($user_category === "Super Admin" || $user_category === "Admin")) {
            if ($jobTitle === "Zonal Head" && $zone) {
                $q->where('zone', $zone);
            } elseif ($jobTitle === "Branch Manager" && $branch) {
                $q->where('branch', $branch);
            } else {
                // Counselor level
                $leadOwner = $empCode . '*' . $empName;
                $q->where('counselor', $leadOwner);
            }
        }

        $rows = $q->orderBy('record_date', 'asc')->get();

        // ---- Static lists ----
        $entityTypes           = ["ISBMU", "ISBM", "ISTM"];
        $transactionMetrics    = ["Total Receipt", "Total Settled", "Total Pending", "SR Sales", "Admissions", "PR Sales"];
        $financialMetrics      = ["Isbmfc001", "Isbmpdc002", "Isbmpdcfc003", "Isbmoc004", "Isbmrc005", "Isbmpc006"];
        $financialMetricsValue = ["Receipt", "Settled", "Pending"];

        // ---- Initialize structures ----
        $groupedTransactions = [];
        foreach ($transactionMetrics as $metric) {
            $groupedTransactions[$metric] = array_fill_keys($entityTypes, 0);
        }
        $groupedEntity       = []; // entity breakdown for selected widget
        $groupedCounselor    = []; // top 10 by total_receipt
        $entityWiseSummary   = []; // counselor-wise detailed summary

        // Collect employee codes for batch user lookup
        $employeeCodes = [];

        // First pass: aggregate
        foreach ($rows as $entry) {
            $entity    = $entry->entity;
            $counselor = $entry->counselor;

            // Ensure entity buckets exist
            if (!array_key_exists($entity, $groupedTransactions["Total Receipt"])) {
                foreach ($transactionMetrics as $metric) {
                    $groupedTransactions[$metric][$entity] = 0;
                }
            }

            // Sum top-level metrics
            $groupedTransactions["Total Receipt"][$entity] += (float) $entry->total_receipt;
            $groupedTransactions["Total Settled"][$entity] += (float) $entry->total_settled;
            $groupedTransactions["Total Pending"][$entity] += (float) $entry->total_pending;
            $groupedTransactions["Admissions"][$entity]    += (float) $entry->admission_count;
            $groupedTransactions["SR Sales"][$entity]      += (float) $entry->sr_sales_amount;
            $groupedTransactions["PR Sales"][$entity]      += (float) $entry->pr_sales_amount;

            // Widget-specific entity breakdown
            if ($widgetNameValue !== '' && $widgetNameValue === (string) $entity) {
                foreach ($financialMetrics as $metric) {
                    foreach ($financialMetricsValue as $value) {
                        if (!isset($groupedEntity[$metric][$value])) {
                            $groupedEntity[$metric][$value] = 0;
                        }
                        $dbColumn = strtolower("{$metric}_{$value}");
                        if (isset($entry->$dbColumn)) {
                            $groupedEntity[$metric][$value] += (float) $entry->$dbColumn;
                        }
                    }
                }
            }

            // Counselor init
            if (!isset($groupedCounselor[$counselor])) {
                $groupedCounselor[$counselor] = [
                    'counselor'     => $counselor,
                    'total_receipt' => 0.0,
                    'profile_picture' => '',
                    'branch'        => '',
                    'zone'          => '',
                    'gender'        => '',
                ];
            }

            // Track counselor meta (branch/zone latest due to ASC order)
            $groupedCounselor[$counselor]['branch'] = $entry->branch;
            $groupedCounselor[$counselor]['zone']   = $entry->zone;

            // Aggregate total receipt for ranking
            $groupedCounselor[$counselor]['total_receipt'] += (float) $entry->total_receipt;

            // Prepare entityWiseSummary structure
            if (!isset($entityWiseSummary[$counselor])) {
                $entityWiseSummary[$counselor] = [];
                foreach ($transactionMetrics as $m) {
                    $entityWiseSummary[$counselor][$m] = 0;
                }
                foreach ($financialMetrics as $m) {
                    foreach ($financialMetricsValue as $v) {
                        $entityWiseSummary[$counselor]["$m $v"] = 0;
                    }
                }
                $entityWiseSummary[$counselor]['branch'] = 0;
            }

            // Update info (last values win due to ASC order)
            $entityWiseSummary[$counselor]['branch']      = $entry->branch;
            $entityWiseSummary[$counselor]['zone']        = $entry->zone;
            $entityWiseSummary[$counselor]['entity']      = $entry->entity;
            $entityWiseSummary[$counselor]['record_date'] = $entry->record_date;

            // Sum general transaction metrics
            $entityWiseSummary[$counselor]["Total Receipt"] += (float) $entry->total_receipt;
            $entityWiseSummary[$counselor]["Total Settled"] += (float) $entry->total_settled;
            $entityWiseSummary[$counselor]["Total Pending"] += (float) $entry->total_pending;
            $entityWiseSummary[$counselor]["Admissions"]    += (float) $entry->admission_count;
            $entityWiseSummary[$counselor]["SR Sales"]      += (float) $entry->sr_sales_amount;
            $entityWiseSummary[$counselor]["PR Sales"]      += (float) $entry->pr_sales_amount;

            // Sum financial breakdown
            foreach ($financialMetrics as $m) {
                foreach ($financialMetricsValue as $v) {
                    $dbColumn = strtolower("{$m}_{$v}");
                    if (isset($entry->$dbColumn)) {
                        $entityWiseSummary[$counselor]["$m $v"] += (float) $entry->$dbColumn;
                    }
                }
            }

            // Collect employee code from counselor string: "code*name"
            if ($counselor) {
                $parts = explode('*', $counselor, 2);
                if (!empty($parts[0])) {
                    $employeeCodes[] = $parts[0];
                }
            }
        }

        // ---- Batch fetch counselor profile pictures & gender ----
        $employeeCodes = array_values(array_unique(array_filter($employeeCodes)));
        if (!empty($employeeCodes)) {
            $userMeta = DB::table('users')
                ->whereIn('employee_code', $employeeCodes)
                ->pluck('profile_picture', 'employee_code')
                ->toArray();

            $userGender = DB::table('users')
                ->whereIn('employee_code', $employeeCodes)
                ->pluck('gender', 'employee_code')
                ->toArray();

            foreach ($groupedCounselor as $counselor => $info) {
                $parts = explode('*', $counselor, 2);
                $code  = $parts[0] ?? null;
                if ($code) {
                    $groupedCounselor[$counselor]['profile_picture'] = $userMeta[$code]   ?? '';
                    $groupedCounselor[$counselor]['gender']          = $userGender[$code] ?? '';
                }
            }
        }

        // ---- Top 10 counselors by total_receipt ----
        $groupedCounselorArr = array_values($groupedCounselor);
        usort($groupedCounselorArr, fn($a, $b) => $b['total_receipt'] <=> $a['total_receipt']);
        $groupedCounselorArr = array_slice($groupedCounselorArr, 0, 10);

        // ---- Response (same shape as your script) ----
        $response = [
            'transactions_count'        => $groupedTransactions,
            'entity_wise_count'         => $groupedEntity,
            'counselor_wise_count'      => $groupedCounselorArr,
            'counselor_entity_wise_count' => $entityWiseSummary,
        ];

        return response()->json($response);
    }

    public function counsellorDashboard()
    {
        return view('dashboard.counsellorDashboard');
    }

    public function counsellorStats(Request $request)
    {
        $dateRange = (string) $request->query('dateRange', '');
        [$startRaw, $endRaw] = array_pad(explode('*', urldecode($dateRange)), 2, null);

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startRaw)->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m-d', $endRaw)->toDateString();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid date format'], 422);
        }

        // ---- Session context ----
        $userCategory = session('user_category');            // e.g. Admin / Team Leader / Group Leader / etc.
        $employeeCode = session('employee_code');
        $employeeName = session('employee_name');
        $teamMembers  = (array) session('team_members', []); // expected: [ ['employee_code'=>..., 'employee_name'=>...], ... ]

        // ---- Base query (matches your original columns) ----
        $q = DB::table('lead_data_log')
            ->select([
                'log_id',
                'task',
                'employee_id',
                'followup_date',
                'remark_by_caller',
                'lead_stage',
                'lead_sub_stage',
                'created_at',
                'updated_by',
            ])
            ->whereNotIn('updated_by', ['System', 'API', ''])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->orderBy('created_at', 'asc');
        $leadOwners = [];

        if ($userCategory === "Super Admin" || $userCategory === "Admin") {
        } elseif ($userCategory === "Group Leader" || $userCategory === "Team Leader") {
            foreach ($teamMembers as $member) {
                $leadOwners[] = ($member['employee_name'] ?? '') . '*' . ($member['employee_code'] ?? '');
            }
        } else {
            $leadOwner = ($employeeCode ?? '') . '*' . ($employeeName ?? '');
            $q->where('employee_id', $leadOwner);
        }

        $rows = $q->get();

        $data = $rows->map(function ($r) {
            return [
                'log_id'           => $r->log_id,
                'task'             => $r->task,
                'employee_id'      => $r->employee_id,
                'followup_date'    => $r->followup_date,
                'remark_by_caller' => $r->remark_by_caller,
                'lead_stage'       => $r->lead_stage,
                'lead_sub_stage'   => $r->lead_sub_stage,
                'created_at'       => $r->created_at,
                'updated_by'       => $r->updated_by,
            ];
        })->all();

        // Count per (employee_id * updated_by) like original
        $employeeLogCount = [];
        foreach ($data as $entry) {
            $key = $entry['employee_id'] . '*' . $entry['updated_by'];
            if (!isset($employeeLogCount[$key])) {
                $employeeLogCount[$key] = 0;
            }
            $employeeLogCount[$key]++;
        }

        $response = [
            'employee_log_count' => $employeeLogCount,
            'data'               => $data,
        ];

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function communicationDashboard()
    {
        return view('dashboard.communicationDashboard');
    }

    public function communicationStats(Request $request)
    {        // ---- Parse & validate dateRange ----
        $dateRange = (string) $request->query('dateRange', '');
        [$startRaw, $endRaw] = array_pad(explode('*', urldecode($dateRange)), 2, null);

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startRaw)->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m-d', $endRaw)->toDateString();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid date format'], 422);
        }

        // ---- Session context ----
        $userCategory = session('user_category');          // "Super Admin" | "Admin" | "Group Leader" | "Team Leader" | ...
        $employeeCode = session('employee_code');          // current user code
        $employeeName = session('employee_name');          // current user name (not used here)
        $teamMembers  = (array) session('team_members', []); // [ ['employee_code'=>..., 'employee_name'=>...], ... ]

        // ---- Base query (matches your selected columns) ----
        $q = DB::table('call_log')
            ->select([
                'id',
                'number',
                DB::raw('DATE(`date`) as date'),
                'duration',
                'type',
                'category',
                'employee_code',
                'created',
            ])
            ->whereBetween(DB::raw('DATE(`date`)'), [$startDate, $endDate])
            ->orderBy('date', 'asc');

        // ---- Role filter using user_category ----
        if ($userCategory === "Super Admin" || $userCategory === "Admin") {
            // no restrictions
        } elseif ($userCategory === "Group Leader" || $userCategory === "Team Leader") {
            // Extract team member employee codes
            $codes = array_values(array_filter(array_map(
                fn($m) => $m['employee_code'] ?? null,
                $teamMembers
            )));

            // If you also want to include the leader's own data, uncomment:
            // if ($employeeCode) { $codes[] = $employeeCode; }

            if (!empty($codes)) {
                $q->whereIn('employee_code', array_unique($codes));
            } else {
                // No team members -> return no rows
                $q->whereRaw('1=0');
            }
        } else {
            // Default: only this user's data
            if ($employeeCode) {
                $q->where('employee_code', $employeeCode);
            } else {
                $q->whereRaw('1=0');
            }
        }

        // ---- Execute ----
        $rows = $q->get();

        // ---- Build arrays similar to original script ----
        $data = $rows->map(function ($r) {
            return [
                'id'            => $r->id,
                'number'        => $r->number,
                'date'          => $r->date,        // already DATE() in query
                'duration'      => (int) $r->duration,
                'type'          => $r->type,
                'category'      => $r->category,
                'employee_code' => $r->employee_code,
                'created'       => $r->created,
            ];
        })->all();

        // 1) call_date_count
        $callDateCount = [];
        foreach ($data as $entry) {
            $callDate = $entry['date'];
            $callDateCount[$callDate] = ($callDateCount[$callDate] ?? 0) + 1;
        }

        // 2) call_type_count_by_employee
        $callTypeCountByEmployee = [];
        foreach ($data as $entry) {
            $emp = $entry['employee_code']; // use employee_code as key
            $type = strtolower((string) $entry['type']);
            $duration = (int) $entry['duration'];

            if (!isset($callTypeCountByEmployee[$emp])) {
                $callTypeCountByEmployee[$emp] = [
                    "success_outgoing"  => 0,
                    "missed_outgoing"   => 0,
                    "total_outgoing"    => 0,
                    "success_incoming"  => 0,
                    "missed_incoming"   => 0,
                    "total_incoming"    => 0,
                ];
            }

            if ($type === "outgoing") {
                if ($duration > 0) {
                    $callTypeCountByEmployee[$emp]["success_outgoing"]++;
                } else {
                    $callTypeCountByEmployee[$emp]["missed_outgoing"]++;
                }
                $callTypeCountByEmployee[$emp]["total_outgoing"]++;
            } elseif ($type === "incoming") {
                if ($duration > 0) {
                    $callTypeCountByEmployee[$emp]["success_incoming"]++;
                } else {
                    $callTypeCountByEmployee[$emp]["missed_incoming"]++;
                }
                $callTypeCountByEmployee[$emp]["total_incoming"]++;
            } elseif ($type === "rejected") {
                $callTypeCountByEmployee[$emp]["missed_outgoing"]++;
                $callTypeCountByEmployee[$emp]["total_outgoing"]++;
            } elseif ($type === "missed") {
                $callTypeCountByEmployee[$emp]["missed_incoming"]++;
                $callTypeCountByEmployee[$emp]["total_incoming"]++;
            }
        }

        // 3) call_details_by_employee (+ percentages)
        $callDetailsByEmployee = [];
        foreach ($data as $entry) {
            $emp = $entry['employee_code'];
            $durationMin = (int) floor($entry['duration'] / 60);
            $category = (string) $entry['category'];

            if (!isset($callDetailsByEmployee[$emp])) {
                $callDetailsByEmployee[$emp] = [
                    'total_calls'                 => 0,
                    'total_duration'              => 0, // minutes
                    'personal_calls'              => 0,
                    'mnql_calls'                  => 0,
                    'mql_calls'                   => 0,
                    'registered_lead_calls'       => 0,
                    // percentages added later
                ];
            }

            $callDetailsByEmployee[$emp]['total_calls']++;
            $callDetailsByEmployee[$emp]['total_duration'] += $durationMin;

            if ($category === 'Personal') {
                $callDetailsByEmployee[$emp]['personal_calls']++;
            } elseif ($category === 'Registered Lead') {
                $callDetailsByEmployee[$emp]['registered_lead_calls']++;
            } elseif ($category === 'Marketing Qualified Lead') {
                $callDetailsByEmployee[$emp]['mql_calls']++;
            } elseif ($category === 'Marketing Non Qualified Lead') {
                $callDetailsByEmployee[$emp]['mnql_calls']++;
            }
        }

        foreach ($callDetailsByEmployee as $emp => &$d) {
            $total = (int) $d['total_calls'];
            if ($total > 0) {
                $d['personal_calls_percentage']          = (int) floor(($d['personal_calls'] / $total) * 100);
                $d['registered_lead_calls_percentage']   = (int) floor(($d['registered_lead_calls'] / $total) * 100);
                $d['mql_calls_percentage']               = (int) floor(($d['mql_calls'] / $total) * 100);
                $d['mnql_calls_percentage']              = (int) floor(($d['mnql_calls'] / $total) * 100);
            } else {
                $d['personal_calls_percentage']          = 0;
                $d['registered_lead_calls_percentage']   = 0;
                $d['mql_calls_percentage']               = 0;
                $d['mnql_calls_percentage']              = 0;
            }
        }
        unset($d);

        $response = [
            'call_date_count'             => $callDateCount,
            'call_type_count_by_employee' => $callTypeCountByEmployee,
            'call_details_by_employee'    => $callDetailsByEmployee,
        ];

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function marketingDashboard()
    {
        return view('dashboard.marketingDashboard');
    }

    public function marketingStats(Request $request)
    {
        $dateRange = (string) $request->query('dateRange', '');
        [$startRaw, $endRaw] = array_pad(explode('*', urldecode($dateRange)), 2, null);

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startRaw)->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m-d', $endRaw)->toDateString();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid date format'], 422);
        }

        // --- Session context ---
        $userCategory = session('user_category');          // "Super Admin" | "Admin" | "Group Leader" | "Team Leader" | ...
        $employeeCode = session('employee_code');
        $employeeName = session('employee_name');
        $teamMembers  = (array) session('team_members', []); // [ ['employee_code'=>..., 'employee_name'=>...], ... ]

        // --- Base query on raw_data (aggregated exactly like your SQL) ---
        $q = DB::table('raw_data')
            ->select([
                'lead_owner',
                'branch',
                'zone',
                // totals
                DB::raw('COUNT(DISTINCT id) AS total_leads'),
                DB::raw("SUM(CASE WHEN lead_status IS NULL THEN 1 ELSE 0 END) AS untouched_count"),
                DB::raw("SUM(CASE WHEN lead_status = 'Convert into Lead' THEN 1 ELSE 0 END) AS sql_count"),
                DB::raw("
                    SUM(
                      CASE WHEN lead_status IN (
                        'Doc Shared','Doc Approved','Fees Negotiation','Phone Counselling Done',
                        'Client Visit Done','Walk In Expected','Brochure Shared','Website Link Shared',
                        'Course details shared','Will Take after 2-6 months'
                      ) THEN 1 ELSE 0 END
                    ) AS mql_count
                "),
                DB::raw("
                    SUM(
                      CASE WHEN lead_status IN (
                        'Another University','Another Branch/Counselor','Not Interested','Wrong No',
                        'Course not available','Enrolled elsewhere','Not enquired','Looking for a job',
                        'Not Eligible','Duplicate Lead','Ringing','Switch Off','Not reachable'
                      ) THEN 1 ELSE 0 END
                    ) AS nql_count
                "),
                DB::raw("SUM(CASE WHEN lead_source = 'Shine'  THEN 1 ELSE 0 END) AS shine_leads_count"),
                DB::raw("SUM(CASE WHEN lead_source = 'Naukri' THEN 1 ELSE 0 END) AS naukri_leads_count"),

                // mirrors your duplicates
                DB::raw("SUM(CASE WHEN lead_status = 'Convert into Lead' THEN 1 ELSE 0 END) AS leads_interested"),
                DB::raw("SUM(CASE WHEN lead_status = 'Convert into Lead' THEN 1 ELSE 0 END) AS leads_enrollments"),

                DB::raw("SUM(CASE WHEN lead_status IS NULL     THEN 1 ELSE 0 END) AS pending_to_work"),
                DB::raw("SUM(CASE WHEN lead_status IS NOT NULL THEN 1 ELSE 0 END) AS worked"),
                DB::raw("SUM(CASE WHEN lead_status = 'Not Interested' THEN 1 ELSE 0 END) AS leads_not_interested"),
            ])
            ->whereBetween(DB::raw('DATE(assigned_date)'), [$startDate, $endDate]);

        // --- Role filter (user_category) ---
        if ($userCategory === 'Super Admin' || $userCategory === 'Admin') {
            // no restrictions
        } elseif ($userCategory === 'Group Leader' || $userCategory === 'Team Leader') {
            // Build "code*name" strings (this dataset uses lead_owner like "EMP001*John Doe")
            $leadOwners = [];
            foreach ($teamMembers as $m) {
                $code = $m['employee_code'] ?? null;
                $name = $m['employee_name'] ?? null;
                if ($code && $name) {
                    $leadOwners[] = $code . '*' . $name;
                }
            }

            if (!empty($leadOwners)) {
                $q->whereIn('lead_owner', array_unique($leadOwners));
            } else {
                // No team => no rows
                $q->whereRaw('1=0');
            }
        } else {
            // Logged-in user only
            if ($employeeCode && $employeeName) {
                $own = $employeeCode . '*' . $employeeName;
                $q->where('lead_owner', $own);
            } else {
                $q->whereRaw('1=0');
            }
        }


        // --- Grouping and ordering (as per your SQL) ---
        $rows = $q->groupBy('lead_owner', 'branch', 'zone')
            ->orderBy('lead_owner', 'asc')
            ->get();

        return response()->json($rows, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
