<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExamAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exam_id' => 1,
            'student_id' => 1,
            'checked_by' => 1, // teacher
            'status' => fake()->randomElement(['present', 'absent']),
        ];
    }
}