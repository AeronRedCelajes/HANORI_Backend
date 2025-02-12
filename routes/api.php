<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileStudentController;
use App\Http\Controllers\Api\ProfileTeacherController;
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

    // 📌 Profile Student Routes (All Singular)
    Route::get('/profile/student/{student}', [ProfileStudentController::class, 'show']);
    Route::put('/profile/student/{student}', [ProfileStudentController::class, 'update']); 
    Route::delete('/profile/student/{student}', [ProfileStudentController::class, 'destroy']);

    // 📌 Profile Teacher Routes (All Singular)
    Route::get('/profile/teacher/{teacher}', [ProfileTeacherController::class, 'show']);
    Route::put('/profile/teacher/{teacher}', [ProfileTeacherController::class, 'update']); 
    Route::delete('/profile/teacher/{teacher}', [ProfileTeacherController::class, 'destroy']);

});