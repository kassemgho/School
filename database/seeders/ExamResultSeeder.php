<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamStudentResult;
use App\Models\Exam;
use App\Models\Student;

class ExamResultSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Student::all() as $student) {
            ExamStudentResult::create([
                'exam_id' => Exam::first()->id,
                'student_id' => $student->id,
                'total_mark' => rand(40, 100),
                'status' => 'submitted',
            ]);
        }
    }
}