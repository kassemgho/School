<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['First','Second','Third','Fourth','Baccalaureate']),
            'level' => fake()->numberBetween(1, 5),
        ];
    }
}