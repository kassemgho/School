<?php

namespace App\Http\Controllers\Api\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class MentorScheduleController extends Controller
{
    /*
    |----------------------------
    | 1. STORE WEEKLY SCHEDULE
    |----------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'schedules' => 'required|array'
        ]);

        DB::beginTransaction();
        foreach ($request->schedules as $item) {

            /*
            |----------------------------
            | 🔴 CHECK DIVISION CONFLICT
            |----------------------------
            */
            $divisionConflict = Schedule::where('division_id', $request->division_id)
                ->where('day_of_week', $item['day_of_week'])
                ->where(function ($q) use ($item) {
                    $q->where('start_time', '<', $item['end_time'])
                        ->where('end_time', '>', $item['start_time']);
                })
                ->exists();

            if ($divisionConflict) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Division time conflict detected'
                ], 422);
            }

            /*
            |----------------------------
            | 🔴 CHECK TEACHER CONFLICT
            |----------------------------
            */
            $teacherConflict = Schedule::where('teacher_id', $item['teacher_id'])
                ->where('day_of_week', $item['day_of_week'])
                ->where(function ($q) use ($item) {
                    $q->where('start_time', '<', $item['end_time'])
                        ->where('end_time', '>', $item['start_time']);
                })
                ->exists();

            if ($teacherConflict) {
                return response()->json([
                    'message' => 'Teacher already assigned at this time'
                ], 422);
            }

            /*
            |----------------------------
            | 🟢 INSERT
            |----------------------------
            */
            Schedule::create([
                'division_id' => $request->division_id,
                'subject_id' => $item['subject_id'],
                'teacher_id' => $item['teacher_id'],
                'day_of_week' => $item['day_of_week'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
            ]);
        }
        DB::commit();
        return response()->json([
            'message' => 'Schedule created successfully'
        ]);
    }

    /*
    |----------------------------
    | 2. VIEW SCHEDULE
    |----------------------------
    */
    public function show($divisionId)
    {
        $schedule = Schedule::with(['subject', 'teacher.user'])
            ->where('division_id', $divisionId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return response()->json([
            'data' => $schedule->map(function ($daySchedules) {

                return $daySchedules->map(function ($s) {
                    return [
                        'start' => $s->start_time,
                        'end' => $s->end_time,
                        'subject' => $s->subject->name ?? null,
                        'teacher' => $s->teacher->user->name ?? null,
                    ];
                });
            })
        ]);
    }
}
