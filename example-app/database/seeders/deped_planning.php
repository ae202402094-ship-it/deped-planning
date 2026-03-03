<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherRanking;

class TeacherRankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            ['stage' => 'Beginning', 'title' => 'Teacher I', 'salary_grade' => 11, 'count' => 0],
            ['stage' => 'Beginning', 'title' => 'Teacher II', 'salary_grade' => 12, 'count' => 0],
            ['stage' => 'Proficient', 'title' => 'Teacher III', 'salary_grade' => 13, 'count' => 0],
            ['stage' => 'Proficient', 'title' => 'Teacher IV', 'salary_grade' => 14, 'count' => 0],
            ['stage' => 'Proficient', 'title' => 'Teacher V', 'salary_grade' => 15, 'count' => 0],
            ['stage' => 'Proficient', 'title' => 'Teacher VI', 'salary_grade' => 16, 'count' => 0],
            ['stage' => 'Proficient', 'title' => 'Teacher VII', 'salary_grade' => 17, 'count' => 0],
            ['stage' => 'Highly Proficient', 'title' => 'Master Teacher I', 'salary_grade' => 18, 'count' => 0],
        ];

        foreach ($ranks as $rank) {
            Deped_Planning::create($rank);
        }
    }
}