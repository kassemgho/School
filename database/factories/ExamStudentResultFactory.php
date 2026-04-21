<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExamStudentResultFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exam_id' => 1,
            'student_id' => 1,
            'total_mark' => fake()->numberBetween(0, 100),
            'status' => fake()->randomElement(['absent','present','submitted']),
        ];
    }
}