<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\ExamStudentResult;
use App\Models\Answer;

class StudentExamController extends Controller
{
    /*
    |----------------------------
    | 1. LIST (exam/test/homework)
    |----------------------------
    */
    public function index(Request $request)
    {
        $student = $request->student;
        $type = $request->type;

        $items = Exam::where('class_id', $student->division->class_id)
            ->where('type', $type)
            ->where(function ($q) use ($student) {
                $q->whereNull('division_id')
                  ->orWhere('division_id', $student->division_id);
            })
            ->orderBy('start_time')
            ->get();

        return response()->json($items);
    }

    /*
    |----------------------------
    | 2. VIEW
    |----------------------------
    */
    public function show(Request $request, $id)
    {
        $student = $request->student;

        $exam = Exam::with('questions')->findOrFail($id);

        // belongs check
        if (!$this->belongsToStudent($exam, $student)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // time check (view)
        if (!$this->canView($exam)) {
            return response()->json([
                'message' => $exam->type . ' not available yet'
            ], 403);
        }

        // attendance (only for exam)
        if ($exam->type === 'exam' && !$this->isPresent($exam, $student)) {
            return response()->json([
                'message' => 'You are absent for this exam'
            ], 403);
        }

        return response()->json($exam);
    }

    /*
    |----------------------------
    | 3. SUBMIT
    |----------------------------
    */
    public function submit(Request $request, $id)
    {
        $student = $request->student;

        $exam = Exam::with('questions')->findOrFail($id);

        // belongs check
        if (!$this->belongsToStudent($exam, $student)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // submit time check
        if (!$this->canSubmit($exam)) {
            return response()->json([
                'message' => ucfirst($exam->type) . ' is not active'
            ], 403);
        }

        // attendance (only exam)
        if ($exam->type === 'exam' && !$this->isPresent($exam, $student)) {
            return response()->json([
                'message' => 'You are not allowed to take this exam'
            ], 403);
        }

        // prevent duplicate
        if ($this->alreadySubmitted($exam, $student)) {
            return response()->json([
                'message' => 'Already submitted'
            ], 400);
        }

        /*
        |----------------------------
        | PROCESS
        |----------------------------
        */

        $answersInput = $request->answers;
        $totalMark = 0;

        $result = ExamStudentResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'total_mark' => 0,
            'status' => 'submitted',
        ]);

        foreach ($exam->questions as $question) {

            $selected = $answersInput[$question->id] ?? '-';

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

        $result->update([
            'total_mark' => $totalMark
        ]);

        return response()->json([
            'message' => ucfirst($exam->type) . ' submitted successfully',
            'student_mark' => $totalMark,
            'exam_mark' => $exam->total_marks
        ]);
    }

    /*
    |====================================================
    | 🔥 HELPER METHODS (CLEAN ARCHITECTURE)
    |====================================================
    */

    private function belongsToStudent($exam, $student)
    {
        return $exam->class_id === $student->division->class_id &&
            (!$exam->division_id || $exam->division_id === $student->division_id);
    }

    private function canView($exam)
    {
        if ($exam->type === 'homework') {
            return now()->lte($exam->end_time);
        }

        return now()->gte($exam->start_time);
    }

    private function canSubmit($exam)
    {
        if ($exam->type === 'homework') {
            return now()->lte($exam->end_time);
        }

        return now()->between($exam->start_time, $exam->end_time);
    }

    private function isPresent($exam, $student)
    {
        $attendance = ExamAttendance::where([
            'exam_id' => $exam->id,
            'student_id' => $student->id
        ])->first();

        return $attendance && $attendance->status === 'present';
    }

    private function alreadySubmitted($exam, $student)
    {
        return ExamStudentResult::where([
            'exam_id' => $exam->id,
            'student_id' => $student->id
        ])->exists();
    }
}