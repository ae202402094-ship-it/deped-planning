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
        'school_id',
        'name',
        'no_of_teachers',
        'no_of_enrollees',
        'no_of_classrooms',
        'no_of_toilets',
        'hazard_type',   // Added for Assessment
        'hazard_level',  // Added for Assessment
        'latitude',
        'longitude',
    ];

    public $incrementing = false; 
    protected $keyType = 'string';
}