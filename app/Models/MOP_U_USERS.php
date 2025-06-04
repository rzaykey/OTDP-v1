<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MOP_U_USERS extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $connection = 'MSADMIN';
    protected $table = 'MOP_U_USERS'; // Add this to prevent Laravel from auto-formatting the name

    protected $fillable = [
       "ID",
        "USERNAME",
        "NAME",
        "EMAIL",
        "EMAIL_VERIFIED_AT",
        "SITE",
        "ROLE",
        "STATUS",
        "PASSWORD",
        "COMPANY",
        "CREATEDBY",
        "CREATEDDTTM",
        "UPDATEDBY",
        "UPDATEDDTTM",
        "REMEMBER_TOKEN",
        "CREATED_AT",
        "UPDATED_AT",
        "STATUS_VERIFIED"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
