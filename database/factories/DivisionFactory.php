<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'class_id' => 1, // will override in seeder
            'name' => 'Div' . fake()->numberBetween(1, 5),
        ];
    }
}