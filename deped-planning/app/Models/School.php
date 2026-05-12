<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    // ADD THE NEW COLUMNS HERE
    protected $fillable = [
        'school_id', 
        'name', 
        'sector',          // <-- Added
        'school_level',    // <-- Added
        'district',        // <-- Added
        'no_of_teachers', 
        'no_of_enrollees', 
        'no_of_classrooms', 
        'no_of_chairs', 
        'no_of_toilets', 
        'latitude', 
        'longitude',
        'with_electricity', 
        'with_potable_water', 
        'with_internet',
        'classroom_shortage', 
        'chair_shortage', 
        'toilet_shortage',
        'teacher_shortage',
        'classroom_ratio',
        'chair_ratio',
        'toilet_ratio',
        'teacher_ratio',
        'hazard_type', 
    ];

    public $incrementing = false; 
    protected $keyType = 'string';
    
    protected $casts = [
        'hazard_type' => 'array', 
    ];
}