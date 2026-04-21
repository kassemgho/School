<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademicYearSeeder::class,
            UserSeeder::class,
            SchoolStructureSeeder::class,
            StudentTeacherSeeder::class,
            SubjectClassificationSeeder::class,
            DivisionTeacherSubjectSeeder::class,
            ScheduleSeeder::class,
            ExamSeeder::class,
            QuestionSeeder::class,
            ExamStudentResultSeeder::class,
            AnswerSeeder::class,
            PostSeeder::class,
            BookSeeder::class,
            AttendanceSeeder::class,
            ExamAttendanceSeeder::class,
        ]);
    }
}