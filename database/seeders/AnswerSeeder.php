<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Answer;
use App\Models\ExamStudentResult;
use App\Models\Question;

class AnswerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ExamStudentResult::all() as $result) {
            foreach (Question::all() as $question) {
                Answer::create([
                    'exam_student_result_id' => $result->id,
                    'question_id' => $question->id,
                    'selected_answer' => 'A',
                    'is_correct' => true,
                ]);
            }
        }
    }
}