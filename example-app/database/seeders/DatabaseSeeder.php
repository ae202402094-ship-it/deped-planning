<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
public function run(): void
{
    $this->call([
        deped_planning::class,
    ]);

    // 2. Insert the Teacher Ranking Data
    DB::table('teacher_rankings')->insert([
            ['career_stage' => 'Beginning', 'position_title' => 'Teacher I', 'salary_grade' => 11, 'teacher_count' => 20],
            ['career_stage' => 'Beginning', 'position_title' => 'Teacher II', 'salary_grade' => 12, 'teacher_count' => 15],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher III', 'salary_grade' => 13, 'teacher_count' => 10],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher IV', 'salary_grade' => 14, 'teacher_count' => 5],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher V', 'salary_grade' => 15, 'teacher_count' => 3],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher VI', 'salary_grade' => 16, 'teacher_count' => 2],
            ['career_stage' => 'Proficient', 'position_title' => 'Teacher VII', 'salary_grade' => 17, 'teacher_count' => 1],
            ['career_stage' => 'Highly Proficient', 'position_title' => 'Master Teacher I', 'salary_grade' => 18, 'teacher_count' => 2],
            ['career_stage' => 'Highly Proficient', 'position_title' => 'Master Teacher II', 'salary_grade' => 19, 'teacher_count' => 1],
        ]);
    }
}