<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'subject_id' => 1, // will be overridden in seeder
            'uploaded_by' => 1, // user id
            'file_path' => 'books/sample.pdf',
        ];
    }
}