<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\SubjectClassification;

class SubjectClassificationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Subject::all() as $subject) {
            SubjectClassification::create([
                'subject_id' => $subject->id,
                'name' => 'Basics',
            ]);
        }
    }
}