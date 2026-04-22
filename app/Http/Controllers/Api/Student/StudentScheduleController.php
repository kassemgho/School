<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Carbon\Carbon;

class StudentScheduleController extends Controller
{
    /*
    |----------------------------
    | 1. FULL WEEK SCHEDULE
    |----------------------------
    */
    public function index(Request $request)
    {
        $student = $request->student;

        $schedule = Schedule::with(['subject', 'teacher'])
            ->where('division_id', $student->division_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return response()->json($schedule);
    }

    /*
    |----------------------------
    | 2. TODAY SCHEDULE
    |----------------------------
    */
    public function today(Request $request)
    {
        $student = $request->student;

        $today = strtolower(Carbon::now()->format('D')); // mon, tue...

        $schedule = Schedule::with(['subject', 'teacher'])
            ->where('division_id', $student->division_id)
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        return response()->json($schedule);
    }
}