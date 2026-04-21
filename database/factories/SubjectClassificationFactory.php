<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectClassificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_id' => 1,
            'name' => fake()->randomElement([
                'Algebra',
                'Geometry',
                'Analysis',
                'Grammar',
                'Physics Basics'
            ]),
        ];
    }
}