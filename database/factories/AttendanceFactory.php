<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => 1,
            'date' => fake()->date(),
            'status' => fake()->randomElement(['present','absent','late']),
            'type' => 'daily',
        ];
    }
}