<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    protected $fillable = ['level_name', 'section_count', 'male_count', 'female_count'];
}