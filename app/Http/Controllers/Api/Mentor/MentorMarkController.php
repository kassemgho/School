<?php

namespace App\Http\Controllers\Api\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\ExamStudentResult;
use App\Models\Answer;

class MentorMarkController extends Controller
{
    /*
    |---------------------------------------
    | 📊 DIVISION MARKS + ANALYSIS
    |---------------------------------------
    */
    public function divisionMarks(Request $request, $divisionId)
    {
        
        $subjectId = $request->subject_id;

        /*
        |---------------------------------------
        | 1. GET RESULTS
        |---------------------------------------
        */
        $results = ExamStudentResult::with([
            'student.user',
            'exam.subject'
        ])
            ->whereHas('student', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            })
            ->when($subjectId, function ($q) use ($subjectId) {
                $q->whereHas('exam', function ($q2) use ($subjectId) {
                    $q2->where('subject_id', $subjectId);
                });
            })
            ->get();

        /*
        |---------------------------------------
        | 2. GROUP BY STUDENT
        |---------------------------------------
        */
        $students = $results->groupBy('student_id')->map(function ($studentResults) {

            $student = $studentResults->first()->student;

            $subjects = $studentResults->groupBy(function ($res) {
                return $res->exam->subject->name ?? 'Unknown';
            })->map(function ($subjectResults) {

                $total = $subjectResults->sum(function ($r) {
                    return $r->exam->total_marks;
                });

                $obtained = $subjectResults->sum('total_mark');

                return [
                    'subject' => $subjectResults->first()->exam->subject->name ?? null,
                    'total_marks' => $total,
                    'obtained' => $obtained,
                    'percentage' => $total > 0 ? round(($obtained / $total) * 100, 2) : 0
                ];
            })->values();

            return [
                'student' => $student->user->name ?? null,
                'subjects' => $subjects
            ];
        })->values();

        /*
        |---------------------------------------
        | 3. CLASSIFICATION ANALYSIS
        |---------------------------------------
        */
        $answers = Answer::with([
            'question.classification',
            'question.exam.subject'
        ])
            ->whereHas('result.student', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            })
            ->when($subjectId, function ($q) use ($subjectId) {
                $q->whereHas('question.exam', function ($q2) use ($subjectId) {
                    $q2->where('subject_id', $subjectId);
                });
            })
            ->get();

        $grouped = $answers->groupBy(function ($a) {
            return optional($a->question->classification)->name ?? 'Unknown';
        });

        $classificationStats = [];

        foreach ($grouped as $name => $group) {
            $total = $group->count();
            $correct = $group->where('is_correct', true)->count();

            $classificationStats[] = [
                'classification' => $name,
                'total' => $total,
                'correct' => $correct,
                'accuracy' => $total > 0
                    ? round(($correct / $total) * 100, 2)
                    : 0
            ];
        }

        /*
        |---------------------------------------
        | FINAL RESPONSE
        |---------------------------------------
        */
        return response()->json([
            'division_id' => $divisionId,
            'students' => $students,
            'classification_analysis' => $classificationStats
        ]);
    }
}
