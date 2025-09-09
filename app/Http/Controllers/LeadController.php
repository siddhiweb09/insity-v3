<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\RegisteredLead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    // Map slugs in the URL to values in DB (lead_stage)
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

    public function index(Request $request, ?string $category = null)
    {
        $session = session();

        if (!empty($session->get('table') && $session->get('table') === "registered_leads")) {
            // dd($session->get('leads'));

            return view('leads.index', [
                'leads'     => $session->get('leads'),
                'category'  => $session->get('category') ?? 'all',
                'stageName' => $session->get('stageName') ?? 'All',
                'categories' => array_keys(self::STAGE_MAP),
            ]);
        }

        $raw = (string) $request->query('date_range', ''); // e.g. "YYYY-MM-DD*YYYY-MM-DD"

        if (preg_match('/^\d{4}-\d{2}-\d{2}\*\d{4}-\d{2}-\d{2}$/', $raw)) {
            [$fromDate, $toDate] = explode('*', $raw, 2);
        } else {
            // defaults: last 7 days
            $fromDate = Carbon::today()->subDays(7)->toDateString();
            $toDate   = Carbon::today()->toDateString();
        }

        // (optional) ensure from <= to
        if (Carbon::parse($fromDate)->gt(Carbon::parse($toDate))) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }


        $dateSource = $request->query('date_source', 'lead_assignment_date');

        $stage = $category ? (self::STAGE_MAP[$category] ?? null) : null;
        if ($category && !$stage) {
            abort(404); // unknown category slug
        }

        // dd($fromDate, $toDate, $dateSource, $stage);

        $employeeCode = $session->get('employee_code'); // matches your original script
        $employeeName = $session->get('employee_name');
        $user_category = $session->get('user_category');

        $q = RegisteredLead::query();

        // optional filters (dates, source, owner, etc.)
        if ($dateSource) {
            $q->whereBetween($dateSource, [$fromDate, $toDate]);
        }

        $leadOwners = [];

        if ($user_category === "Super Admin" || $user_category === "Admin") {
            // no restrictions
        } elseif ($user_category === "Group Leader" || $user_category === "Team Leader") {
            foreach (session('team_members', []) as $member) {
                $leadOwners[] = $member['employee_name'] . "*" . $member['employee_code'];
            }
            $q->whereIn('lead_owner', $leadOwners);
        } else {
            $leadOwner = $employeeCode . "*" . $employeeName;
            $q->where('lead_owner', $leadOwner);
        }

        // apply category (single page logic)
        if ($stage) {
            $q->where('lead_stage', $stage);
        }

        $leads = $q->latest($dateSource)->get();

        return view('leads.index', [
            'leads'     => $leads,
            'category'  => $category ?? 'all',
            'stageName' => $stage ?? 'All',
            'categories' => array_keys(self::STAGE_MAP), // for tabs/links
        ]);
    }

    public function reassign(Request $request)
    {
        $request->validate([
            'lead_id'       => ['required'],
            'employee_code' => ['required', 'string'],
        ]);

        $raw = $request->input('lead_id');
        $pieces = is_array($raw)
            ? $raw
            : preg_split('/\s*,\s*/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);

        $leadIds = collect($pieces)
            ->map(function ($v) {
                if (str_contains($v, '*')) {
                    return trim(explode('*', $v, 2)[0]);
                }
                return trim($v);
            })
            ->filter(fn($id) => $id !== '')
            ->unique()
            ->values()
            ->all();

        if (empty($leadIds)) {
            return response()->json(['ok' => false, 'message' => 'No valid lead IDs provided.'], 422);
        }

        $empInput = $request->input('employee_code');
        if (str_contains($empInput, '*')) {
            [$employeeCode, $employeeName] = explode('*', $empInput, 2);
            $employeeCode = trim($employeeCode);
            $employeeName = trim($employeeName);
        } else {
            $userRow = DB::table('users')->where('employee_code', $empInput)->first();
            if (!$userRow) {
                return response()->json(['ok' => false, 'message' => 'Assignee not found.'], 404);
            }
            $employeeCode = $empInput;
            $employeeName = $userRow->employee_name;
        }
        $newLeadOwner = $employeeCode . '*' . $employeeName;

        $assignee = DB::table('users')
            ->select('branch', 'zone', 'telegram_chat_id', 'telegram_token')
            ->where('employee_code', $employeeCode)
            ->first();

        $assigneeBranch = $assignee->branch ?? null;
        $assigneeZone   = $assignee->zone ?? null;
        $chatId         = $assignee->telegram_chat_id ?? null;
        $botToken       = $assignee->telegram_token ?? null;

        if (empty($employeeCode) || $employeeCode === 'NA') {
            $chatId   = env('TELEGRAM_FALLBACK_CHAT_ID', '-4249457056');
            $botToken = env('TELEGRAM_FALLBACK_BOT_TOKEN');
        }

        $actorCode = Auth::user()->employee_code ?? session('employee_code') ?? 'SYSTEM';
        $actorName = Auth::user()->employee_name ?? session('employee_name') ?? 'System';
        $actorPair = $actorCode . '*' . $actorName;

        $now = now('Asia/Kolkata')->format('Y-m-d H:i:s');
        $updatedTotal = 0;

        DB::beginTransaction();

        try {
            foreach ($leadIds as $id) {
                // Get one lead (to derive mobile + existing info)
                $lead = DB::table('registered_leads')
                    ->select(
                        'id',
                        'registered_mobile',
                        'registered_name',
                        'registered_email',
                        'branch',
                        'zone',
                        'lead_owner',
                        'log_id',
                        'reassigned_count'
                    )
                    ->where('id', $id)
                    ->first();

                if (!$lead || empty($lead->registered_mobile)) {
                    // Skip if not found or no mobile to key on (legacy uses mobile as the matching key)
                    continue;
                }

                $mobile     = $lead->registered_mobile;
                $oldOwner   = $lead->lead_owner ?? 'NA';
                $logId      = $lead->log_id ?? (string) $id;
                $newCount   = (int) ($lead->reassigned_count ?? 0) + 1;

                // Insert into reassign_lead_data (like legacy)
                DB::table('reassign_lead_data')->insert([
                    'registered_name'     => $lead->registered_name,
                    'registered_email'    => $lead->registered_email,
                    'registered_mobile'   => $mobile,
                    'branch'              => $assigneeBranch ?? $lead->branch,
                    'zone'                => $assigneeZone ?? $lead->zone,
                    'lead_owner'          => $newLeadOwner,
                    'reassigned_from'     => $oldOwner,
                    'assign_reassigned_by' => $actorPair,
                    'reassigned_on'       => $now,
                ]);

                // Update ALL rows with the same registered_mobile (legacy behavior)
                $updated = DB::table('registered_leads')
                    ->where('registered_mobile', $mobile)
                    ->where('registered_mobile', '!=', '')
                    ->update([
                        'activity_from'           => 'WebAPP',
                        'branch'                  => $assigneeBranch ?? $lead->branch,
                        'zone'                    => $assigneeZone ?? $lead->zone,
                        'lead_owner'              => $newLeadOwner,
                        'last_lead_activity_date' => $now,
                        'reassigned'              => $newLeadOwner,
                        'assign_reassigned_by'    => $actorPair,
                        'reassigned_on'           => $now,
                        'reassigned_count'        => $newCount,
                        'updated_at'              => $now,
                    ]);
                $updatedTotal += $updated;

                // Insert into lead_data_log
                $task = "{$actorPair} reassigned this lead from {$oldOwner} to {$newLeadOwner}";
                DB::table('lead_data_log')->insert([
                    'log_id'        => $logId,
                    'task'          => $task,
                    'employee_id'   => $actorCode,
                    'created_at'    => $now,
                    'updated_by'    => $actorName,
                    'activity_from' => 'WebAPP',
                ]);

                // Telegram message (if both present)
                if (!empty($chatId) && !empty($botToken)) {
                    $checkEntryId = base64_encode((string) $id);
                    $message = "You have been reassigned a lead: <b>{$lead->registered_name}</b>";

                    $replyMarkup = [
                        'inline_keyboard' => [[
                            ['text' => 'Lead', 'url' => url("lead-details?param={$checkEntryId}")],
                        ]],
                    ];

                    try {
                        Http::asJson()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                            'chat_id'      => $chatId,
                            'text'         => $message,
                            'parse_mode'   => 'HTML',
                            'reply_markup' => json_encode($replyMarkup),
                        ]);

                        // Log telegram
                        DB::table('logs_telegram')->insert([
                            'log_id'  => $logId,
                            'message' => $message,
                            'chat_id' => $chatId,
                            'created' => $now,
                        ]);
                    } catch (\Throwable $e) {
                        // Mirror your legacy error logging
                        DB::table('mysqli_error_logs')->insert([
                            'mysqli_error' => $e->getMessage(),
                            'page'         => 'Telegram sendMessage reassign_lead',
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // Mirror legacy error log
            DB::table('mysqli_error_logs')->insert([
                'mysqli_error' => $e->getMessage(),
                'page'         => 'UPDATE registered_leads reassign_lead',
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'Reassign failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'ok'            => true,
            'updated_count' => $updatedTotal,
            'lead_ids'      => $leadIds,
            'new_owner'     => $newLeadOwner,
        ]);
    }

    public function recommendation(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'lead_id'        => ['required', 'string'],   // may be "123*Name" or just "123"
            'recommendation' => ['required', 'string'],
            'url'            => ['required', 'string'],   // if it's not always a full URL, keep as string
        ]);

        $now = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');

        // Who added this recommendation
        $actorCode = Auth::user()->employee_code ?? session('employee_code') ?? 'SYSTEM';
        $actorName = Auth::user()->employee_name ?? session('employee_name') ?? 'System';
        $addedBy   = $actorCode . '*' . $actorName;

        // Extract numeric id from lead_id (handles "123*Name")
        $leadIdRaw = $data['lead_id'];
        $leadId    = Str::before($leadIdRaw, '*') ?: $leadIdRaw;

        // Fetch lead to get owner + log_id
        $lead = DB::table('registered_leads')
            ->select('id', 'lead_owner', 'log_id')
            ->where('id', $leadId)
            ->first();

        if (!$lead) {
            return $request->wantsJson()
                ? response()->json(['ok' => false, 'message' => 'Lead not found.'], 404)
                : redirect()->to($data['url'])->with('error', 'Lead not found.');
        }

        $leadOwner = (string) ($lead->lead_owner ?? '');
        $logId     = (string) ($lead->log_id ?? (string)$leadId);

        // Parse lead owner "EMP001*Alice"
        $ownerCode = Str::before($leadOwner, '*') ?: $leadOwner;

        // Insert recommendation
        DB::table('recommendations')->insert([
            'log_id'         => $logId,
            'recommendation' => $data['recommendation'],
            'added_on'       => $now,
            'added_by'       => $addedBy,
            'lead_owner'     => $leadOwner,
        ]);

        // Notify lead owner (Telegram + optional push)
        $user = DB::table('users')
            ->select('telegram_chat_id', 'telegram_token', 'firebase_token')
            ->where('employee_code', $ownerCode)
            ->first();

        $chatId   = $user->telegram_chat_id ?? null;
        $botToken = $user->telegram_token   ?? null;
        $deviceToken = $user->firebase_token ?? null;

        $message = "You have new recommendation for {$leadIdRaw} from {$addedBy}";

        // Telegram inline keyboard linking to the provided URL
        $replyMarkup = [
            'inline_keyboard' => [[
                ['text' => 'Lead', 'url' => $data['url']],
            ]],
        ];

        if (!empty($chatId) && !empty($botToken)) {
            try {
                Http::asJson()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id'      => $chatId,
                    'text'         => $message,
                    'parse_mode'   => 'HTML',
                    'reply_markup' => json_encode($replyMarkup),
                ]);

                // Log telegram
                DB::table('logs_telegram')->insert([
                    'log_id'  => $logId,
                    'message' => $message,
                    'chat_id' => $chatId,
                    'created' => $now,
                ]);
            } catch (\Throwable $e) {
                // Mirror your legacy error logging table
                DB::table('mysqli_error_logs')->insert([
                    'mysqli_error' => $e->getMessage(),
                    'page'         => 'sendMessage recommendations',
                ]);
            }
        }

        // Optional: Firebase push (if you have a helper function like in legacy)
        try {
            $result = app(\App\Http\Controllers\NotificationController::class)
                ->sendNotificationV1(
                    title: 'New Recommendation',
                    message: 'You have a new recommendation',
                    token: $deviceToken,
                    lastInsertId: $logId,
                    targetActivity: 'LeadDetails'
                );
        } catch (\Throwable $e) {
            DB::table('mysqli_error_logs')->insert([
                'mysqli_error' => $e->getMessage(),
                'page'         => 'push_notification recommendations',
            ]);
        }

        // Respond (AJAX vs normal POST)
        if ($request->ajax()) {
            return response()->json([
                'ok'            => true,
                'message'       => 'Recommendation added.',
                'lead_id'       => $leadId,
                'log_id'        => $logId,
                'redirect_to'   => $data['url'],
            ]);
        }

        return redirect()->to($data['url'])->with('status', 'Recommendation added.');
    }
}
