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
    'no_of_chairs', // Added
    'no_of_toilets', 
    'latitude', 
    'longitude',
    'with_electricity', 
    'with_potable_water', 
    'with_internet',
    'classroom_shortage', 
    'chair_shortage', 
    'toilet_shortage',
    'hazards'
];

    public $incrementing = false; 
    protected $keyType = 'string';
}