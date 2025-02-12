<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import Log Facade

class ProfileTeacherController extends Controller
{
    // 📌 GET Teacher Profile
    public function show(Teacher $teacher)
    {
        Log::info("Teacher Profile Accessed", ['teacherID' => $teacher->teacherID]);
        return response()->json($teacher, 200);
    }

    // 📌 UPDATE Teacher Profile
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:teachers,email,' . $teacher->teacherID,
            'password' => 'sometimes|string|min:8',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'coverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profileImage')) {
            if ($teacher->profileImage) {
                Storage::delete($teacher->profileImage);
            }
            $validated['profileImage'] = $request->file('profileImage')->store('profile_images', 'public');
        }

        // Handle cover image upload
        if ($request->hasFile('coverImage')) {
            if ($teacher->coverImage) {
                Storage::delete($teacher->coverImage);
            }
            $validated['coverImage'] = $request->file('coverImage')->store('cover_images', 'public');
        }

        // Hash password if provided
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $teacher->update($validated);
        Log::info("Teacher Profile Updated", ['teacherID' => $teacher->teacherID]);

        return response()->json(['message' => 'Profile updated successfully', 'teacher' => $teacher], 200);
    }

    // 📌 DELETE Teacher Profile
    public function destroy(Teacher $teacher)
    {
        // Delete stored images if they exist
        if ($teacher->profileImage) {
            Storage::delete($teacher->profileImage);
        }
        if ($teacher->coverImage) {
            Storage::delete($teacher->coverImage);
        }

        $teacher->delete();
        Log::warning("Teacher Profile Deleted", ['teacherID' => $teacher->teacherID]);

        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }
}