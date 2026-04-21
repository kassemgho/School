<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Exam;
use App\Models\SubjectClassification;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $exam = Exam::first();

        for ($i = 0; $i < 10; $i++) {
            Question::create([
                'exam_id' => $exam->id,
                'classification_id' => SubjectClassification::first()->id,
                'question_text' => 'Sample question ' . $i,
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_answer' => 'A',
                'mark' => 10,
            ]);
        }
    }
}