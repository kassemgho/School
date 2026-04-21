<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\AcademicYear;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        Exam::create([
            'title' => 'Math Exam',
            'type' => 'exam',
            'subject_id' => Subject::first()->id,
            'teacher_id' => Teacher::first()->id,
            'class_id' => SchoolClass::first()->id,
            'division_id' => null,
            'academic_year_id' => AcademicYear::first()->id,
            'total_marks' => 100,
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ]);
    }
}