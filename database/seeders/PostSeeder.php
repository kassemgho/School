<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::where('role', 'manager')->get() as $user) {
            Post::create([
                'user_id' => $user->id,
                'title' => 'School Announcement',
                'content' => 'Welcome to the new semester',
                'type' => 'announcement',
            ]);
        }
    }
}