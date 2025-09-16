<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarMenu extends Model
{
    protected $fillable = [
        'name',
        'url',
        'categories',
        'icons',
        'created_at',
        'updated_at'
    ];

    protected $table = 'sidebar_menus';

}
