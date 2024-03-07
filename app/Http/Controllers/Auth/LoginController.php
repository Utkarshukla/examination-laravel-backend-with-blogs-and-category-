<?php

// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;



class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            return response()->json(['user' => $user, 'token' => $token]);
        }

        throw ValidationException::withMessages([
            'email' => ['Invalid credentials'],
        ]);
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to log'], 500);
        }
    }

}
