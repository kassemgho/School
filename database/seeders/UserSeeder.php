<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Managers
        User::create([
            'name' => 'Admin Manager',
            'email' => 'admin@school.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);
        User::create([
            'name' => 'Mentor',
            'email' => 'mentor@school.com',
            'password' => bcrypt('password'),
            'role' => 'mentor',
        ]);

        // Teachers
        User::factory(10)->create(['role' => 'teacher']);

        // Students
        User::factory(50)->create(['role' => 'student']);
    }
}