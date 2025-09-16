<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeadDataLog;
use App\Models\Recommendation;

class RegisteredLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_id',
        'registered_name',
        'registered_email',
        'registered_mobile',
        'alternate_mobile',
        'state',
        'city',
        'level_applying_for',
        'course',
        'lead_status',
        'lead_stage',
        'lead_sub_stage',
        'lead_remark',
        'lead_owner',
        'lead_source',
        'widget_name',
        'branch',
        'zone',
        'registration_attempts',
        'email_sent_count',
        'sms_sent_count',
        'whatsapp_message_count',
        'followup_count',
        'stage_change_count',
        'outbound_success',
        'outbound_missed',
        'outbound_total',
        'inbound_success',
        'inbound_missed',
        'inbound_total',
        'lead_assignment_date',
        'last_lead_activity_date',
        'lead_followup_date'
    ];

    protected $dates = [
        'lead_assignment_date',
        'last_lead_activity_date',
        'lead_followup_date'
    ];

    public function logs()
    {
        return $this->hasMany(LeadDataLog::class, 'log_id', 'log_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'log_id', 'log_id');
    }
}
