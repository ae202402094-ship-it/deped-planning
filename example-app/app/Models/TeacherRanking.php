<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherRanking extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'career_stage', 'position_title', 'salary_grade', 'teacher_count'];

    // This defines the relationship to the School model
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}