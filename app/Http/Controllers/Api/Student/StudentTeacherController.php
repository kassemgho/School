<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\DivisionTeacherSubject;

class StudentTeacherController extends Controller
{
    /*
    |----------------------------
    | LIST TEACHERS (WITH FILTERS)
    |----------------------------
    */
    public function index(Request $request)
    {
        $student = $request->student;

        $subjectId = $request->subject_id;
        $divisionId = $student->division_id;

        $query = Teacher::with(['user']);

        /*
        |----------------------------
        | FILTER BY SUBJECT
        |----------------------------
        */
        if ($subjectId) {
            $query->whereHas('divisionSubjects', function ($q) use ($subjectId, $divisionId) {
                $q->where('subject_id', $subjectId)
                  ->where('division_id', $divisionId);
            });
        } else {
            /*
            |----------------------------
            | ALL TEACHERS IN STUDENT DIVISION
            |----------------------------
            */
            $query->whereHas('divisionSubjects', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $teachers = $query->get();

        /*
        |----------------------------
        | FORMAT RESPONSE
        |----------------------------
        */
        $result = $teachers->map(function ($teacher) use ($divisionId) {

            $assignments = $teacher->divisionSubjects()
                ->where('division_id', $divisionId)
                ->with(['subject'])
                ->get();

            return [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
                'specialization' => $teacher->specialization,
                'subjects' => $assignments->map(function ($a) {
                    return [
                        'subject_id' => $a->subject->id,
                        'subject_name' => $a->subject->name,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $result
        ]);
    }

    /*
    |----------------------------
    | SINGLE TEACHER PROFILE
    |----------------------------
    */
    public function show($id)
    {
        $teacher = Teacher::with(['user', 'divisionSubjects.subject'])
            ->findOrFail($id);

        return response()->json([
            'id' => $teacher->id,
            'name' => $teacher->user->name,
            'certificate' => $teacher->certificate,
            'specialization' => $teacher->specialization,
            'subjects' => $teacher->divisionSubjects->map(function ($a) {
                return [
                    'subject' => $a->subject->name,
                ];
            }),
        ]);
    }
}