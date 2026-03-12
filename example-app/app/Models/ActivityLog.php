<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'target_name', 'changes'];
    protected $casts = ['changes' => 'array'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}