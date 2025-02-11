<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // Function to determine if the email belongs to a student or a teacher
    private function getUserTypeByEmail($email)
    {
        if (str_ends_with($email, '@student.edu')) {
            return 'student';
        } elseif (str_ends_with($email, '@teacher.edu')) {
            return 'teacher';
        } else {
            return null; // Invalid email format
        }
    }

    public function register(Request $request)
    {
        \Log::info('Register Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => [
                'required', 'email',
                Rule::unique('students', 'email'),
                Rule::unique('teachers', 'email')
            ],
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Determine user type based on email format
        $userType = $this->getUserTypeByEmail($request->email);

        if (!$userType) {
            return response()->json([
                'message' => 'Invalid email format. Use @student.edu or @teacher.edu',
            ], 422);
        }

        // Create user based on type
        if ($userType === 'student') {
            $user = Students::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } else {
            $user = Teachers::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        // Generate API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'user_type' => $userType,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        \Log::info('Login Attempt:', ['email' => $request->email]);
    
        // Validate request input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Determine user type based on email format
        $userType = $this->getUserTypeByEmail($request->email);
    
        if (!$userType) {
            return response()->json([
                'message' => 'Invalid email format. Use @student.edu or @teacher.edu',
            ], 422);
        }
    
        // Find user in respective table
        $user = $userType === 'student' 
            ? Students::where('email', $request->email)->first() 
            : Teachers::where('email', $request->email)->first();
    
        // Unified error message to prevent email enumeration
        if (!$user || !Hash::check($request->password, $user->password)) {
            \Log::warning('Failed login attempt', ['email' => $request->email]);
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }
    
        // Generate API token
        try {
            $token = $user->createToken('auth_token')->plainTextToken;
        } catch (\Exception $e) {
            \Log::error('Token Creation Error:', ['email' => $request->email, 'error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to generate token. Please try again.',
            ], 500);
        }
    
        \Log::info('Login Success', ['email' => $request->email, 'user_type' => $userType]);
    
        // Return success response
        return response()->json([
            'message' => 'Login Success',
            'user_type' => $userType,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
          

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}