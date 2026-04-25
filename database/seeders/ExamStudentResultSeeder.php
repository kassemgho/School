<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamStudentResult;
use App\Models\Answer;

class ExamStudentResultSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $exams = Exam::with('questions')->get();

        foreach ($students as $student) {

            foreach ($exams as $exam) {

                // 🧠 Skip if exam not for this student
                if (
                    $exam->class_id !== $student->division->class_id ||
                    ($exam->division_id && $exam->division_id !== $student->division_id)
                ) {
                    continue;
                }

                // 🟡 Prevent duplicate
                $exists = ExamStudentResult::where([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id
                ])->exists();

                if ($exists) continue;

                $totalMark = 0;

                // 🟢 Create result
                $result = ExamStudentResult::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'total_mark' => 0,
                    'status' => 'submitted',
                ]);

                foreach ($exam->questions as $question) {

                    // 🎯 Random answer (A,B,C,D)
                    $options = ['A', 'B', 'C', 'D'];
                    $selected = $options[array_rand($options)];

                    $isCorrect = $selected === $question->correct_answer;

                    if ($isCorrect) {
                        $totalMark += $question->mark;
                    }

                    Answer::create([
                        'exam_student_result_id' => $result->id,
                        'question_id' => $question->id,
                        'selected_answer' => $selected,
                        'is_correct' => $isCorrect,
                    ]);
                }

                // 🟢 Update total mark
                $result->update([
                    'total_mark' => $totalMark
                ]);
            }
        }
    }
}