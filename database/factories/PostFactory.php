<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1, // manager/admin user in seeder
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(5),
            'type' => fake()->randomElement(['announcement','system']),
        ];
    }
}   