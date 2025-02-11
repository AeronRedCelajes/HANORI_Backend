<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Students;

class ProfileStudentController extends Controller
{
    // 📌 GET Student Profile
    public function show($id)
    {
        $student = Students::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json($student, 200);
    }

    // 📌 UPDATE Student Profile
    public function update(Request $request, $id)
    {
        $student = Students::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'firstname' => 'sometimes|string',
            'lastname' => 'sometimes|string',
            'email' => 'sometimes|email|unique:students,email,'.$id,
            'profileImage' => 'nullable|string',
            'coverImage' => 'nullable|string',
        ]);

        $student->update($validated);
        return response()->json($student, 200);
    }
}