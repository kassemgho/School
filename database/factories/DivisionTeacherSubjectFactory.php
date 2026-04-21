<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionTeacherSubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'teacher_id' => 1,
            'division_id' => 1,
            'subject_id' => 1,
        ];
    }
}