<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exam_student_result_id' => 1,
            'question_id' => 1,
            'selected_answer' => fake()->randomElement(['A','B','C','D']),
            'is_correct' => fake()->boolean(),
        ];
    }
}   