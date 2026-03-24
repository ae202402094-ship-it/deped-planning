<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;
    // 1. Ensure these match your controller's updateOrCreate array
    protected $fillable = [
        'school_id', 'name', 'latitude', 'longitude', 
        'no_of_teachers', 'no_of_enrollees', 'no_of_classrooms', 'no_of_toilets'
    ];


    // 2. Add this if school_id is your main identifier and NOT an auto-incrementing number
    public $incrementing = false; 
    protected $keyType = 'string';
}