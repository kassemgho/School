<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Mentor\MentorMarkController;
use App\Http\Controllers\Api\Mentor\MentorResultController;
use App\Http\Controllers\Api\Mentor\MentorScheduleController;
use App\Http\Controllers\Api\Mentor\MentorStudentController;
use App\Http\Controllers\Api\Mentor\MentorTeacherController;
use App\Http\Controllers\Api\Mentor\PaymentController;
use App\Http\Controllers\Api\Student\StudentAttendanceController;
use App\Http\Controllers\Api\Student\StudentBookController;
use App\Http\Controllers\Api\Student\StudentDashboardController;
use App\Http\Controllers\Api\Student\StudentExamController;
use App\Http\Controllers\Api\Student\StudentPostController;
use App\Http\Controllers\Api\Student\StudentResultController;
use App\Http\Controllers\Api\Student\StudentScheduleController;
use App\Http\Controllers\Api\Student\StudentTeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * * * * * * * * * * * * * * * * *
 *                               *
 *  NO REGISTER IN THE SYSTEM    *
 *                               *
 * * * * * * * * * * * * * * * * *   
 */

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});



Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    // here will be the manager APIs
});

Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    // here will be the teacher APIs
});

Route::prefix('/student')->middleware(['auth:sanctum', 'student'])->group(function () {

    Route::get('/dashboard', [StudentDashboardController::class, 'index']);
    //schedule 
    Route::get('/schedule', [StudentScheduleController::class, 'index']);
    Route::get('/schedule/today', [StudentScheduleController::class, 'today']);

    //exam 
    Route::post('/exams', [StudentExamController::class, 'index']);
    Route::post('/exams/{id}', [StudentExamController::class, 'show']);
    Route::post('/exams/{id}/submit', [StudentExamController::class, 'submit']);

    //result
    Route::get('/results', [StudentResultController::class, 'index']);
    Route::get('/results/{id}', [StudentResultController::class, 'show']);
    Route::get('/results-analysis', [StudentResultController::class, 'analysis']);
    Route::get('/results-subject-analysis', [StudentResultController::class, 'subjectAnalysis']);

    //teachers
    Route::get('/teachers', [StudentTeacherController::class, 'index']);
    Route::get('/teachers/{id}', [StudentTeacherController::class, 'show']);

    //posts
    Route::get('/posts', [StudentPostController::class, 'index']);
    Route::get('/posts/{id}', [StudentPostController::class, 'show']);

    //books
    Route::get('/books', [StudentBookController::class, 'index']);
    Route::get('/books/{id}/download', [StudentBookController::class, 'download']);

    //attendance
    Route::get('/attendance', [StudentAttendanceController::class, 'index']);

    //test
    Route::get('/test', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'student_id' => $request->student->id,
            'division' => $request->student->division->name,
            'class' => $request->student->division->class->name,
        ]);
    });
});

Route::prefix('/mentor')->middleware(['auth:sanctum', 'role:mentor'])->group(function () {
    Route::get('/teachers', [MentorTeacherController::class, 'index']);
    Route::get('/teachers/{id}', [MentorTeacherController::class, 'show']);


    Route::get('/classes', [MentorStudentController::class, 'classes']);
    Route::get('/classes/{classId}/divisions', [MentorStudentController::class, 'divisions']);
    Route::get('/divisions/{divisionId}/students', [MentorStudentController::class, 'students']);

    // Route::get('/divisions/{divisionId}/attendance', [MentorStudentController::class, 'attendance']); // and this 

    //marks
    Route::get('/divisions/{divisionId}/marks', [MentorMarkController::class, 'divisionMarks']);

    Route::post('/attendance', [MentorStudentController::class, 'store']);   // create attendance 
    Route::get('/attendance', [MentorStudentController::class, 'show']);     // load for edit   // this one 
    Route::patch('/attendance/{id}', [MentorStudentController::class, 'update']); // update student

    //schedule
    Route::post('/schedules', [MentorScheduleController::class, 'store']);
    Route::get('/divisions/{divisionId}/schedule', [MentorScheduleController::class, 'show']);

    //payments
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/student/{studentId}', [PaymentController::class, 'history']);
    Route::get('/payments/{studentFeeId}', [PaymentController::class, 'show']);
    // here will be the mentor APIs
});
