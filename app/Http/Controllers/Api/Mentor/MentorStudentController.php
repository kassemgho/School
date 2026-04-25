<?php

namespace App\Http\Controllers\Api\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Division;
use App\Models\Student;
use App\Models\Attendance;

class MentorStudentController extends Controller
{
    /**
    |----------------------------
    | 1. LIST CLASSES
    |----------------------------
     */
    public function classes()
    {
        return response()->json([
            'data' => SchoolClass::select('id', 'name')->get()
        ]);
    }

    /**
    |----------------------------
    | 2. DIVISIONS BY CLASS
    |----------------------------
     */
    public function divisions($classId)
    {
        $divisions = Division::where('class_id', $classId)
            ->select('id', 'name')
            ->get();

        return response()->json([
            'data' => $divisions
        ]);
    }

    /**
    |----------------------------
    | 3. STUDENTS BY DIVISION
    |----------------------------
     */
    public function students($divisionId)
    {
        $students = Student::with('user')
            ->where('division_id', $divisionId)
            ->get();

        return response()->json([
            'data' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                ];
            })
        ]);
    }

    /**
    |----------------------------
    | 4. ATTENDANCE BY DIVISION
    |----------------------------
     */
    public function attendance(Request $request, $divisionId)
    {
        $date = $request->date;
        $type = $request->type; // daily / exam
        if ($date == null) {
            $date = now()->toDateString(); // YYYY-MM-DD
        }
        $attendance = Attendance::with(['student.user'])
            ->where('division_id', $divisionId)
            ->when($date, function ($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', 'daily');
            })
            ->get();

        return response()->json([
            'data' => $attendance->map(function ($item) {
                return [
                    'student_id' => $item->student_id,
                    'student_name' => $item->student->user->name ?? null,
                    'status' => $item->status,
                    'type' => $item->type,
                    'date' => $item->date,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $divisionId = $request->division_id;
        $date = $request->date ?? now()->toDateString();
        if ($date == null) {
            $date = now()->toDateString(); // YYYY-MM-DD
        }
        // 🔴 Check if already exists
        $exists = Attendance::where('division_id', $divisionId)
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Attendance already taken, switch to edit mode'
            ], 409);
        }

        // 🔵 Get students
        $students = Student::where('division_id', $divisionId)->get();

        $data = [];

        foreach ($students as $student) {
            $data[] = [
                'student_id' => $student->id,
                'division_id' => $divisionId,
                'date' => $date,
                'type' => 'daily',
                'status' => 'present', // default
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Attendance::insert($data);

        return response()->json([
            'message' => 'Attendance created successfully',
        ]);
    }
    /**
     |-----------------------
     |📌 2. LOAD FOR EDITING
     |-----------------------
     */
    
    public function show(Request $request)
    {
        $divisionId = $request->division_id;
        $date = $request->date ?? now()->toDateString();
        $type = 'daily';
        if ($date == null) {
            $date = now()->toDateString(); // YYYY-MM-DD
        }
        $attendance = \App\Models\Attendance::with('student.user')
            ->where('division_id', $divisionId)
            ->whereDate('date', $date)
            ->where('type', $type)
            ->get();

        return response()->json([
            'data' => $attendance->map(function ($a) {
                return [
                    'id' => $a->id,
                    'student_id' => $a->student_id,
                    'student_name' => $a->student->user->name ?? null,
                    'status' => $a->status,
                ];
            })
        ]);
    }
    /**
     |-----------------------
     |📌 3. UPDATE SINGLE STUDENT
     |-----------------------
     */
    public function update( Request $request,$id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);

        $request->validate([
            'status' => 'required|in:present,absent,late'
        ]);

        $attendance->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Attendance updated'
        ]);
    }
}
