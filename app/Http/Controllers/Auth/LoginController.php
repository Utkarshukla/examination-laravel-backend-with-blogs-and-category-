<?php

// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials'],
                ]);
            }

            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            return response()->json(['status'=>'success','user' => $user, 'token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['status'=>'failure','error' => $e->getMessage()], 401);
        }
    }
    
    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate(); 
            return response()->json(['status'=>'success','message' => 'Logged out successfully']);
        } catch (TokenInvalidException $e) {
            return response()->json(['status'=>'failure','error' => 'Invalid token'], 401);
        } catch (\Exception $e) {
            return response()->json(['status'=>'failure','error' => 'Failed to log out'], 500);
        }
    }

    public function refreshToken()
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            return response()->json(['status' => 'success', 'token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failure', 'error' => 'Failed to refresh token'], 401);
        }
    }
    public function verifyEmail(Request $request)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();
        $token = Str::random(60);
        if($user->email_verified_at !=null){
            return response()->json(['status'=>'success','message'=>'Mail is already Verified.']);
        }
        
        $user->remember_token = $token;
        $user->save();
        
        Mail::to($user->email)->send(new VerifyEmail($user));
        
        return response()->json(['status'=>'success','message' => 'Verification link Send successfully']);
    } catch (\Exception $e) {
       return response()->json(['status' => 'failure', 'message' => 'Unauthorized'], 401);
    }
}

public function verifyEmailToken(Request $request, $email ,$token)
{
    try {
        $user = User::where('email','=',$email)->first();
        echo $user->remember_token;
        if ($user->remember_token === $token) {
            $user->email_verified_at = now();
            $user->remember_token = null;
            $user->save();
            echo 'Successfully verified';
            return response()->json(['status'=>'success', 'redirect-url'=>'/']);
        } else {
            return response()->json(['status'=>'failure','message' => 'Invalid token'], 400);
        }
    } catch (\Exception $e) {
        return response()->json(['status'=>'failure','message' => 'Unauthorized'], 401);
    }
}
}
