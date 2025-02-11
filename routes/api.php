<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProfileStudentController;
use App\Http\Controllers\ProfileTeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 📌 Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register/student', 'registerStudent'); // Register Student
    Route::post('/register/teacher', 'registerTeacher'); // Register Teacher
    Route::post('/login', 'login');                      // Login for both roles
});

// 📌 Protected Routes (Requires Authentication via Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // 🔹 Logout
    Route::post('/logout', [AuthController::class, 'logout']); 

    // 🔹 Get Authenticated User (Student or Teacher)
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'user' => $user,
            'user_type' => $user instanceof \App\Models\Student ? 'student' : 'teacher',
        ]);
    });

    // 📌 Student Profile Routes
    Route::prefix('profile/students')->group(function () {
        Route::get('{student_num}', [ProfileStudentController::class, 'show']);   // Get student profile
        Route::put('{student_num}', [ProfileStudentController::class, 'update']); // Update student profile
    });

    // 📌 Teacher Profile Routes
    Route::prefix('profile/teachers')->group(function () {
        Route::get('{id}', [ProfileTeacherController::class, 'show']);   // Get teacher profile
        Route::put('{id}', [ProfileTeacherController::class, 'update']); // Update teacher profile
    });
});