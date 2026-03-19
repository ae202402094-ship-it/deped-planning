<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes; // Enable Soft Deletes

    protected $fillable = [
        'school_id', 'name', 'latitude', 'longitude', 
        'no_of_teachers', 'no_of_enrollees', 'no_of_classrooms', 'no_of_toilets'
    ];

    public $incrementing = false; 
    protected $keyType = 'string';
}