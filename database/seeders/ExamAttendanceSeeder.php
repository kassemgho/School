<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamAttendance;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;

class ExamAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $exam = Exam::first();
        $teacher = Teacher::first();
        $students = Student::all();

        foreach ($students as $student) {
            ExamAttendance::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'checked_by' => $teacher->id,
                'status' => fake()->randomElement(['present', 'absent']),
            ]);
        }
    }
}