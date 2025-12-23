<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RecruiterController;
use App\Http\Controllers\API\JobPostController;
use App\Http\Controllers\Api\WorkerController;

// ---------------------------------------------
// Default Sanctum User Route
// ---------------------------------------------
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ---------------------------------------------
// Recruiter Authentication Routes
// ---------------------------------------------

// Login (Public)
Route::post('/recruiter/login', [RecruiterController::class, 'login']);

// List recruiters
Route::get('/recruiters', [RecruiterController::class, 'index']);

// Show recruiter by UUID
Route::get('/recruiters/{uuid}', [RecruiterController::class, 'show']);


// ---------------------------------------------
// Job Post Routes (Public & Protected)
// ---------------------------------------------

// Public — anyone can see job posts list & details
Route::get('/job-posts', [JobPostController::class, 'index']);
Route::get('/job-posts/{uuid}', [JobPostController::class, 'show']);


// ---------------------------------------------
// Protected Routes (Require Bearer Token)
// ---------------------------------------------
Route::middleware('auth:sanctum','abilities:create-job')->group(function () {

    Route::post('/recruiter/logout', [RecruiterController::class, 'logout']);
    Route::post('recruiter/jobs', [RecruiterController::class,'jobs']);
    Route::get('/recruiter/profile',[RecruiterController::class, 'profile']);
    Route::post('/recruiter/profile',[RecruiterController::class, 'updateProfile']);
    Route::post('/recruiter/job-posts', [RecruiterController::class, 'createJobPost']);

    // Job Posts — only authenticated recruiters can manage

    Route::put('/job-posts/{uuid}', [JobPostController::class, 'update']);
    Route::delete('/job-posts/{uuid}', [JobPostController::class, 'destroy']);



});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/worker/profile', [WorkerController::class, 'showProfile']);
    Route::post('/worker/profile', [WorkerController::class, 'updateProfile']);
    Route::post('worker/jobs/{uuid}/apply',[WorkerController::class, 'applyForJob']);
});
// ---------------------------------------------
// Worker OTP Authentication (Public)
// ---------------------------------------------
Route::post('/worker/login-otp', [WorkerController::class, 'loginOtp']);

