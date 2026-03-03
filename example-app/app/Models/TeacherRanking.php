<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherRanking extends Model
{
    use HasFactory;

    // Add this line to allow seeding the columns
    protected $fillable = ['career_stage', 'position_title', 'salary_grade', 'teacher_count'];
}