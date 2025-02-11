<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProfileStudentController;
use App\Http\Controllers\ProfileTeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register'); // Universal register route
    Route::post('/login', 'login');       // Universal login route

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout'); // Logout route for authenticated users

        // Get user details (student or teacher)
        Route::get('/user', function (Request $request) {
            return response()->json([
                'user' => $request->user(),
                'user_type' => str_ends_with($request->user()->email, '@student.edu') ? 'student' : 'teacher',
            ]);
        });

        // 📌 Student Profile Routes
        Route::prefix('profile/students')->group(function () {
            Route::get('{id}', [ProfileStudentController::class, 'show']); // Get student profile
            Route::put('{id}', [ProfileStudentController::class, 'update']); // Update student profile
        });

        // 📌 Teacher Profile Routes
        Route::prefix('profile/teachers')->group(function () {
            Route::get('{id}', [ProfileTeacherController::class, 'show']); // Get teacher profile
            Route::put('{id}', [ProfileTeacherController::class, 'update']); // Update teacher profile
        });
    });
});