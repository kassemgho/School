<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;
use App\Models\Subject;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Subject::all() as $subject) {
            Book::create([
                'title' => $subject->name . ' Book',
                'subject_id' => $subject->id,
                'uploaded_by' => User::first()->id,
                'file_path' => 'books/sample.pdf',
            ]);
        }
    }
}