<?php

use App\Http\Controllers\Api\StudentsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('students', StudentsController::class);

Route::controller(StudentsController::class)->group(function()
{
	Route::post('/students/register', 'register');
	Route::post('/students/login', 'login');

	Route::middleware('auth:sanctum')->group(function()
	{
		Route::post('/students/logout', 'logout');
		Route::get('/students', function (Request $request){
			return $request->user();
		});
	});
});