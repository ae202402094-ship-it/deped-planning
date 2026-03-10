<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',   
        'status', 
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // REMOVED 'integer' cast if you are using string statuses like 'pending'/'approved'
        ];
    }

    /**
     * Role & Status Helpers
     */
    public function isSuperAdmin() 
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin() 
    {
        return $this->role === 'admin';
    }

    public function isApproved() 
    {
        // Must match exactly what is in your database 'status' column
        return $this->status === 'approved';
    }

    /**
     * Custom Email Verification
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}