<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentFee>
 */
class StudentFeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = $this->faker->numberBetween(500, 1500);

        return [
            'student_id' => Student::inRandomOrder()->value('id'),
            'academic_year_id' => AcademicYear::inRandomOrder()->value('id'),
            'total_amount' => $total,
            'paid_amount' => 0,
            'status' => 'pending',
        ];
    }
}
