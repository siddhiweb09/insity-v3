<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'group_name',
        'group_zone',
        'group_avatar',
        'group_leader',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $table = 'groups';

}
