<?php

namespace Database\Seeders;

use App\Models\StudentFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentFee::factory()->count(20)->create();
    }
}
