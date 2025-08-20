<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisteredLead extends Model
{
    use HasFactory;

    public function scopeStage($q, ?string $stage)
    {
        return $stage ? $q->where('lead_stage', $stage) : $q;
    }
}
