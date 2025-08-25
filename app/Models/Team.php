<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'team_name',
        'team_leader',
        'group_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $table = 'teams';

}
