<?php

namespace App\Http\Controllers\Api\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;

class MentorTeacherController extends Controller
{
    /**
    |----------------------------
    | LIST ALL TEACHERS WITH ASSIGNMENTS
    |----------------------------
    */
    public function index()
    {
        $teachers = Teacher::with([
            'user',
            'divisionSubjects.division.class',
            'divisionSubjects.subject'
        ])->get();

        $data = $teachers->map(function ($teacher) {

            $assignments = $teacher->divisionSubjects->map(function ($dts) {
                return [
                    'class' => $dts->division->class->name ?? null,
                    'division' => $dts->division->name ?? null,
                    'subject' => $dts->subject->name ?? null,
                ];
            });

            return [
                'id' => $teacher->id,
                'name' => $teacher->user->name ?? null,
                'specialization' => $teacher->specialization,
                'assignments' => $assignments
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }

    /**
    |----------------------------
    | SINGLE TEACHER DETAILS
    |----------------------------
    */
    public function show($id)
    {
        $teacher = Teacher::with([
            'user',
            'divisionSubjects.division.class',
            'divisionSubjects.subject'
        ])->findOrFail($id);

        $assignments = $teacher->divisionSubjects->map(function ($dts) {
            return [
                'class' => $dts->division->class->name ?? null,
                'division' => $dts->division->name ?? null,
                'subject' => $dts->subject->name ?? null,
            ];
        });

        return response()->json([
            'id' => $teacher->id,
            'name' => $teacher->user->name ?? null,
            'certificate' => $teacher->certificate,
            'specialization' => $teacher->specialization,
            'assignments' => $assignments
        ]);
    }
}