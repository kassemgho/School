<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'certificate' => fake()->randomElement(['BA','MA','PHD']),
            'specialization' => fake()->jobTitle(),
            'hire_date' => fake()->date(),
        ];
    }
}