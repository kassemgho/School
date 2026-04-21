<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'division_id' => 1,
            'subject_id' => 1,
            'teacher_id' => 1,
            'day_of_week' => fake()->randomElement(['sun','mon','tue','wed','thu','fri','sat']),
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
        ];
    }
}