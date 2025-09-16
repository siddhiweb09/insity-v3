<?php
// app/Models/LeadDataLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadDataLog extends Model
{
    use HasFactory;

    protected $table = 'lead_data_log';

    protected $fillable = [
        'log_id',
        'task',
        'followup_date',
        'recorded_file'
    ];

    protected $dates = [
        'followup_date'
    ];
}
