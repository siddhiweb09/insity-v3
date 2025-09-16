<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrantPrivilege extends Model
{
    protected $fillable = [
        'pri_group_name',
        'icon',
        'action_buttons',
        'menubar_items',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $table = 'grant_privileges';

}
