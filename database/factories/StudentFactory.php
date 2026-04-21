<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'division_id' => 1,
            'academic_year_id' => 1,
            'enrollment_year' => fake()->year(),
            'status' => 'active',
        ];
    }
}