<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveLeadSource extends Model
{
    protected $fillable = [
        'sources',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $table = 'lead_source';

}
