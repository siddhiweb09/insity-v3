<?php
// app/Models/Task.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreativeTemplates extends Model
{
    protected $fillable = [
        'title',
        'bg_image',
        'image_json',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $table = 'creatives_templates';

}
