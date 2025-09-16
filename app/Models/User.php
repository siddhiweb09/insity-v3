<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // Your table name
    protected $primaryKey = 'id'; // Your primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'employee_name',
        'email_id_personal',
        'mobile_no_personal',
        'dob',
        'gender',
        'pan_card_no',
        'department',
        'job_title_designation',
        'zone',
        'branch',
        'doj',
        'email_id_official',
        'mobile_no_official',
        'created_at',
        'created_by',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pan_card_no', // Hide sensitive data
        'remember_token',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'employee_code'; // Use employee_code as identifier
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->employee_code;
    }
}