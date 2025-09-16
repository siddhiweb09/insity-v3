<?php
// app/Models/Recommendation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'log_id',
        'recommendation',
        'added_by'
    ];
}