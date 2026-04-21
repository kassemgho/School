<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use App\Models\Division;
use App\Models\Subject;

class SchoolStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Classes
        $classes = SchoolClass::insert([
            ['name' => 'First', 'level' => 1],
            ['name' => 'Second', 'level' => 2],
            ['name' => 'Third', 'level' => 3],
        ]);

        $classes = SchoolClass::all();

        // Divisions
        foreach ($classes as $class) {
            for ($i = 1; $i <= 3; $i++) {
                Division::create([
                    'class_id' => $class->id,
                    'name' => 'Div' . $i,
                ]);
            }
        }

        // Subjects
        Subject::insert([
            ['name' => 'Math'],
            ['name' => 'Physics'],
            ['name' => 'English'],
            ['name' => 'Biology'],
        ]);
    }
}