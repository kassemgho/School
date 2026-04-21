<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Student;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Student::all() as $student) {
            Attendance::create([
                'student_id' => $student->id,
                'date' => now(),
                'status' => 'present',
                'type' => 'daily',
            ]);
        }
    }
}