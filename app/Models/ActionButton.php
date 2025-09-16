<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionButton extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'class',
        'categories',
        'purpose',
        'created_at',
        'updated_at'
    ];

    protected $table = 'action_buttons';

}
