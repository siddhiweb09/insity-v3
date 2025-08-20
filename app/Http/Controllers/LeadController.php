<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredLead;

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
        $stage = $category ? (self::STAGE_MAP[$category] ?? null) : null;
        if ($category && !$stage) {
            abort(404); // unknown category slug
        }

        $q = RegisteredLead::query();

        // optional filters (dates, source, owner, etc.)
        if ($request->filled('from') && $request->filled('to')) {
            $q->whereBetween('lead_assignment_date', [$request->input('from'), $request->input('to')]);
        }
        if ($request->filled('lead_source')) {
            $q->where('lead_source', $request->input('lead_source'));
        }

        // apply category (single page logic)
        if ($stage) {
            $q->where('lead_stage', $stage);
        }

        $leads = $q->latest('lead_assignment_date')->paginate(25)->withQueryString();

        return view('leads.index', [
            'leads'     => $leads,
            'category'  => $category ?? 'all',
            'stageName' => $stage ?? 'All',
            'categories'=> array_keys(self::STAGE_MAP), // for tabs/links
        ]);
    }
}
