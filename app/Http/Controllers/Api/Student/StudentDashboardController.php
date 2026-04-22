<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Exam;
use App\Models\Post;
use App\Models\Attendance;
use App\Models\ExamStudentResult;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->student;
        $today = strtolower(Carbon::now()->format('D')); // mon, tue...

        /*
        |----------------------------
        | 1. TODAY SCHEDULE
        |----------------------------
        */
        $todaySchedule = Schedule::with(['subject', 'teacher'])
            ->where('division_id', $student->division_id)
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        /*
        |----------------------------
        | 2. UPCOMING EXAMS
        |----------------------------
        */
        $upcomingExams = Exam::where('class_id', $student->division->class_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('division_id')
                  ->orWhere('division_id', $student->division_id);
            })
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        /*
        |----------------------------
        | 3. LATEST POSTS
        |----------------------------
        */
        $posts = Post::latest()->take(5)->get();

        /*
        |----------------------------
        | 4. ATTENDANCE STATS
        |----------------------------
        */
        $totalAttendance = Attendance::where('student_id', $student->id)->count();

        $presentAttendance = Attendance::where('student_id', $student->id)
            ->where('status', 'present')
            ->count();

        $attendanceRate = $totalAttendance > 0
            ? round(($presentAttendance / $totalAttendance) * 100, 2)
            : 0;

        /*
        |----------------------------
        | 5. LAST EXAM RESULT
        |----------------------------
        */
        $lastResult = ExamStudentResult::with('exam')
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        /*
        |----------------------------
        | FINAL RESPONSE
        |----------------------------
        */
        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->user->name,
                'division' => $student->division->name,
                'class' => $student->division->class->name,
            ],

            'today_schedule' => $todaySchedule,

            'upcoming_exams' => $upcomingExams,

            'latest_posts' => $posts,

            'stats' => [
                'attendance_rate' => $attendanceRate,
                'last_exam' => $lastResult,
            ]
        ]);
    }
}