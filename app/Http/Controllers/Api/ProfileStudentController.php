<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import Log Facade

class ProfileStudentController extends Controller
{
    // 📌 GET Student Profile
    public function show(Student $student)
    {
        Log::info("Student Profile Accessed", ['studentID' => $student->studentID]);
        return response()->json($student, 200);
    }

    // 📌 UPDATE Student Profile
    public function update(Request $request, $studentID)
    {
        // Explicitly find the student using studentID
        $student = Student::where('studentID', $studentID)->first();
    
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
    
        // Validate input
        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students,email,' . $student->studentID . ',studentID',
            'student_num' => 'sometimes|string|unique:students,student_num,' . $student->studentID . ',studentID',
            'program' => 'sometimes|in:BSCS,BSIT,BSEMC,BSIS',
            'password' => 'sometimes|string|min:8',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'coverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Handle profile image upload
        if ($request->hasFile('profileImage')) {
            if ($student->profileImage) {
                Storage::delete($student->profileImage);
            }
            $validated['profileImage'] = $request->file('profileImage')->store('profile_images', 'public');
        }
    
        // Handle cover image upload
        if ($request->hasFile('coverImage')) {
            if ($student->coverImage) {
                Storage::delete($student->coverImage);
            }
            $validated['coverImage'] = $request->file('coverImage')->store('cover_images', 'public');
        }
    
        // Hash password if provided
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        }
    
        $student->update($validated);
        Log::info("Student Profile Updated", ['studentID' => $student->studentID]);
    
        return response()->json(['message' => 'Profile updated successfully', 'student' => $student], 200);
    }
    

    // 📌 DELETE Student Profile
    public function destroy(Student $student)
    {
        // Delete stored images if they exist
        if ($student->profileImage) {
            Storage::delete($student->profileImage);
        }
        if ($student->coverImage) {
            Storage::delete($student->coverImage);
        }

        $student->delete();
        Log::warning("Student Profile Deleted", ['studentID' => $student->studentID]);

        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }
}