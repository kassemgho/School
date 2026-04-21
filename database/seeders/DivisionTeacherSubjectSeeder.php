<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Division;
use App\Models\Subject;
use App\Models\DivisionTeacherSubject;

class DivisionTeacherSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $divisions = Division::all();
        $subjects = Subject::all();

        for ($i = 0; $i < 30; $i++) {
            DivisionTeacherSubject::create([
                'teacher_id' => $teachers->random()->id,
                'division_id' => $divisions->random()->id,
                'subject_id' => $subjects->random()->id,
            ]);
        }
    }
}