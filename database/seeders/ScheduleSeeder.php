<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\DivisionTeacherSubject;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DivisionTeacherSubject::all() as $item) {
            Schedule::create([
                'division_id' => $item->division_id,
                'subject_id' => $item->subject_id,
                'teacher_id' => $item->teacher_id,
                'day_of_week' => fake()->randomElement(['mon','tue','wed','thu','fri']),
                'start_time' => '08:00:00',
                'end_time' => '09:00:00',
            ]);
        }
    }
}