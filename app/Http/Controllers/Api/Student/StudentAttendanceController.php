<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->student;

        $attendance = Attendance::where('student_id', $student->id)->get();

        $total = $attendance->count();

        $present = $attendance->where('status', 'present')->count();
        $absent = $attendance->where('status', 'absent')->count();

        return response()->json([
            'overall' => [
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'present_percentage' => $total ? round(($present / $total) * 100, 2) : 0,
                'absent_percentage' => $total ? round(($absent / $total) * 100, 2) : 0,
            ]
        ]);
    }
}
