<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamStudentResult;
use App\Models\Answer;

class StudentResultController extends Controller
{
    /*
    |----------------------------
    | 1. ALL RESULTS
    |----------------------------
    */
    public function index(Request $request)
    {
        $student = $request->student;

        $results = ExamStudentResult::with('exam')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return response()->json($results);
    }

    /*
    |----------------------------
    | 2. SINGLE RESULT
    |----------------------------
    */
    public function show(Request $request, $id)
    {
        $student = $request->student;

        $result = ExamStudentResult::with([
            'exam',
            'answers.question'
        ])
            ->where('student_id', $student->id)
            ->findOrFail($id);

        return response()->json($result);
    }

    /*
    |----------------------------
    | 3. ANALYSIS 🔥
    |----------------------------
    */
    public function analysis(Request $request)
    {
        $student = $request->student;

        $answers = Answer::with('question.classification')
            ->whereHas('result', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->get();


        /*
        |----------------------------
        | 1. OVERALL STATS
        |----------------------------
        */
        $total = $answers->count();
        $correct = $answers->where('is_correct', true)->count();

        $accuracy = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

        /*
        |----------------------------
        | 2. CLASSIFICATION ANALYSIS
        |----------------------------
        */
        $byClassification = $answers->groupBy(function ($answer) {
            return optional($answer->question->classification)->name ?? 'Unknown';
        });

        $classificationStats = [];

        foreach ($byClassification as $name => $group) {

            $totalQ = $group->count();
            $correctQ = $group->where('is_correct', true)->count();

            $classificationStats[] = [
                'classification' => $name,
                'total' => $totalQ,
                'correct' => $correctQ,
                'accuracy' => $totalQ > 0
                    ? round(($correctQ / $totalQ) * 100, 2)
                    : 0
            ];
        }

        /*
        |----------------------------
        | FINAL RESPONSE
        |----------------------------
        */
        return response()->json([
            'overall' => [
                'total_questions' => $total,
                'correct_answers' => $correct,
                'accuracy' => $accuracy
            ],
            'classification_analysis' => $classificationStats
        ]);
    }
    public function subjectAnalysis(Request $request)
    {
        $student = $request->student;

        $subjectId = $request->subject_id;

        $answers = Answer::with([
            'question.classification',
            'question.exam.subject'
        ])
            ->whereHas('result', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->when($subjectId, function ($query) use ($subjectId) {
                $query->whereHas('question.exam', function ($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                });
            })
            ->get();
        /*
    |----------------------------
    | GROUP BY SUBJECT
    |----------------------------
    */
        $bySubject = $answers->groupBy(function ($answer) {
            return optional($answer->question->exam->subject)->name ?? 'Unknown';
        });

        $result = [];

        foreach ($bySubject as $subjectName => $group) {

            $totalQ = $group->count();
            $correctQ = $group->where('is_correct', true)->count();

            /*
        |----------------------------
        | CLASSIFICATION INSIDE SUBJECT 🔥
        |----------------------------
        */
            $byClassification = $group->groupBy(function ($answer) {
                return optional($answer->question->classification)->name ?? 'Unknown';
            });

            $classificationStats = [];

            foreach ($byClassification as $className => $classGroup) {

                $cTotal = $classGroup->count();
                $cCorrect = $classGroup->where('is_correct', true)->count();

                $classificationStats[] = [
                    'classification' => $className,
                    'total' => $cTotal,
                    'correct' => $cCorrect,
                    'accuracy' => $cTotal > 0
                        ? round(($cCorrect / $cTotal) * 100, 2)
                        : 0
                ];
            }

            $result[] = [
                'subject' => $subjectName,
                'total' => $totalQ,
                'correct' => $correctQ,
                'accuracy' => $totalQ > 0
                    ? round(($correctQ / $totalQ) * 100, 2)
                    : 0,
                'classifications' => $classificationStats
            ];
        }

        return response()->json($result);
    }
}
