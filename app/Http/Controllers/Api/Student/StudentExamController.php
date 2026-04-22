<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\ExamStudentResult;
use App\Models\Answer;
use Carbon\Carbon;

class StudentExamController extends Controller
{
    /*
    |----------------------------
    | 1. LIST AVAILABLE EXAMS
    |----------------------------
    */
    public function index(Request $request)
    {
        $student = $request->student;
        // return $request->type ; 
        $exams = Exam::where('class_id', $student->division->class_id)
            ->where('type', $request->type)
            ->where(function ($q) use ($student) {
                $q->whereNull('division_id')
                    ->orWhere('division_id', $student->division_id);
            })
            ->orderBy('start_time')
            ->get();

        return response()->json($exams);
    }

    /*
    |----------------------------
    | 2. VIEW EXAM (WITH QUESTIONS)
    |----------------------------
    */
    public function show(Request $request, $id)
    {
        $student = $request->student;

        $exam = Exam::with('questions')
            ->findOrFail($id);

        // 🔴 Check belongs to student
        if (
            $exam->class_id !== $student->division->class_id ||
            ($exam->division_id && $exam->division_id !== $student->division_id)
        ) {
            return response()->json(['message' => 'Unauthorized exam'], 403);
        }

        // 🔴 Check start time
        if (now()->lt($exam->start_time)) {
            return response()->json([
                'message' => $exam->type . ' not started yet'
            ], 403);
        }

        // 🔴 Check attendance
        $attendance = ExamAttendance::where([
            'exam_id' => $exam->id,
            'student_id' => $student->id
        ])->first();

        if ((!$attendance || $attendance->status !== 'present' ) && $exam->type == 'exam') {
            return response()->json([
                'message' => 'You are absent for this exam'
            ], 403);
        }

        return response()->json($exam);
    }

    /*
    |----------------------------
    | 3. SUBMIT EXAM
    |----------------------------
    */
    public function submit(Request $request, $id)
    {
        $student = $request->student;

        $exam = Exam::with('questions')->findOrFail($id);

        /*
        |----------------------------
        | VALIDATIONS
        |----------------------------
        */

        // belongs check
        if (
            $exam->class_id !== $student->division->class_id ||
            ($exam->division_id && $exam->division_id !== $student->division_id)
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // time check
        if (now()->lt($exam->start_time) || now()->gt($exam->end_time)) {
            return response()->json([
                'message' => 'Exam is not active'
            ], 403);
        }

        // attendance check
        $attendance = ExamAttendance::where([
            'exam_id' => $exam->id,
            'student_id' => $student->id
        ])->first();

        if ((!$attendance || $attendance->status !== 'present' ) && $exam->type == 'exam') {
            return response()->json([
                'message' => 'You are not allowed to take this exam'
            ], 403);
        }

        // prevent duplicate submission
        $existing = ExamStudentResult::where([
            'exam_id' => $exam->id,
            'student_id' => $student->id
        ])->first();

        if ($existing) {
            // ExamStudentResult::where('exam_id', $exam->id)->delete(); //comment_test - add
            return response()->json([
                'message' => 'Already submitted'
            ], 400);
        }

        /*
        |----------------------------
        | PROCESS ANSWERS
        |----------------------------
        */

        $answersInput = $request->answers; // array

        $totalMark = 0;

        $result = ExamStudentResult::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'total_mark' => 0,
            'status' => 'submitted',
        ]);

        foreach ($exam->questions as $question) {

            $selected = $answersInput[$question->id] ?? "-";

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
            'message' => 'Exam submitted successfully',
            'student_mark' => $totalMark,
            'exam_mark' => $exam->total_marks
        ]);
    }
}
