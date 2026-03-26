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
  // app/Models/School.php
protected $fillable = [
    'school_id', 
    'name', 
    'no_of_teachers', 
    'no_of_enrollees', 
    'no_of_classrooms', 
    'no_of_toilets', 
    'latitude', 
    'longitude',
    'with_electricity',    // MUST BE ADDED
    'with_potable_water',  // MUST BE ADDED
    'with_internet',       // MUST BE ADDED
    'classroom_shortage',  // MUST BE ADDED
    'chair_shortage',      // MUST BE ADDED
    'toilet_shortage',     // MUST BE ADDED
    'hazards'              // MUST BE ADDED
];

    public $incrementing = false; 
    protected $keyType = 'string';
}