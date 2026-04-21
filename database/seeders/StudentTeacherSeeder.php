<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Division;
use App\Models\AcademicYear;

class StudentTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $teachers = User::where('role', 'teacher')->get();

        $divisions = Division::all();
        $year = AcademicYear::first();

        // Students
        foreach ($students as $user) {
            Student::create([
                'user_id' => $user->id,
                'division_id' => $divisions->random()->id,
                'academic_year_id' => $year->id,
                'enrollment_year' => date('Y'),
                'status' => 'active',
            ]);
        }

        // Teachers
        foreach ($teachers as $user) {
            Teacher::create([
                'user_id' => $user->id,
                'certificate' => 'BA',
                'specialization' => 'General',
                'hire_date' => now(),
            ]);
        }
    }
}