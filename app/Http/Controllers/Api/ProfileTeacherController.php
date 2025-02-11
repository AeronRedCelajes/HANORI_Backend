<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class ProfileTeacherController extends Controller
{
    // 📌 GET Teacher Profile
    public function show($id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }
        return response()->json($teacher, 200);
    }

    // 📌 UPDATE Teacher Profile
    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $validated = $request->validate([
            'firstname' => 'sometimes|string',
            'lastname' => 'sometimes|string',
            'email' => 'sometimes|email|unique:teachers,email,'.$id,
            'profileImage' => 'nullable|string',
            'coverImage' => 'nullable|string',
        ]);

        $teacher->update($validated);
        return response()->json($teacher, 200);
    }
}