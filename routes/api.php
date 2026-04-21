<?php

use App\Http\Controllers\Api\AuthController;
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

Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    // here will be the student APIs
});

Route::middleware(['auth:sanctum', 'role:mentor'])->group(function () {
    // here will be the mentor APIs
});

