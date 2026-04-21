<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(['exam','test','homework']),
            'subject_id' => 1,
            'teacher_id' => 1,
            'class_id' => 1,
            'division_id' => null,
            'academic_year_id' => 1,
            'total_marks' => 100,
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ];
    }
}