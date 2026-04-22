<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Student\StudentDashboardController;
use App\Http\Controllers\Api\Student\StudentExamController;
use App\Http\Controllers\Api\Student\StudentScheduleController;
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
    
    
    Route::get('/test', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'student_id' => $request->student->id,
            'division' => $request->student->division->name,
            'class' => $request->student->division->class->name,
        ]);
    });
});

Route::middleware(['auth:sanctum', 'role:mentor'])->group(function () {
    // here will be the mentor APIs
});
