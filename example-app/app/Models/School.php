<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
    'school_id', 'name', 'latitude', 'longitude', 
    'no_of_teachers', 'no_of_enrollees', 'no_of_classrooms', 'no_of_toilets'
];

    public function teacherRankings()
    {
        return $this->hasMany(TeacherRanking::class);
    }
}