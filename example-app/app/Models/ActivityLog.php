<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // These must match the columns in your activity_logs table
    protected $fillable = [
        'user_id', 
        'action', 
        'target_name', 
        'changes'
    ];

    // This tells Laravel to convert the 'changes' array into a JSON string automatically
    protected $casts = [
        'changes' => 'array'
    ];

    /**
     * Relationship to the User who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}