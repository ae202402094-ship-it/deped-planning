<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherRanking;

class deped_planning extends Seeder
{
    public function run(): void
    {
        $ranks = [
            // Changed 'stage' to 'career_stage' and 'title' to 'position_title'
            // Changed 'count' to 'teacher_count'
            ['career_stage' => 'Beginning', 'position_title' => 'Teacher I', 'salary_grade' => 11, 'teacher_count' => 0],
            ['career_stage' => 'Beginning', 'position_title' => 'Teacher II', 'salary_grade' => 12, 'teacher_count' => 0],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher III', 'salary_grade' => 13, 'teacher_count' => 0],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher IV', 'salary_grade' => 14, 'teacher_count' => 0],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher V', 'salary_grade' => 15, 'teacher_count' => 0],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher VI', 'salary_grade' => 16, 'teacher_count' => 0],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher VII', 'salary_grade' => 17, 'teacher_count' => 0],
            ['career_stage' => 'Highly Proficient', 'position_title' => 'Master Teacher I', 'salary_grade' => 18, 'teacher_count' => 0],
        ];

        foreach ($ranks as $rank) {
            // Ensure you use the TeacherRanking model here
            \App\Models\TeacherRanking::create($rank);
        }
    }
}