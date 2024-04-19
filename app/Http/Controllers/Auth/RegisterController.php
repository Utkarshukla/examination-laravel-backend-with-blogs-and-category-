<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $for = $request->register_as_student;
       
        if($for == 1){
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'phone' => ['required'],
                'father' => ['required', 'string', 'max:255'],
                'mother' => ['required', 'string', 'max:255'],
                'class' => ['required', 'numeric'],
                'dob' => ['required', 'date'],
                'city' => ['required', 'string'],
                'district' => ['required', 'string'],
                'pincode' => ['required', 'numeric'],
                'school' => ['required', 'string'],
                'password' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $requestData = $request->all();
            $requestData['role'] = 5;
            unset($requestData['register_as_student']);
            $user =User::create($requestData);
            $token = JWTAuth::fromUser($user);
            return response()->json(['status'=>'success','user' => $user, 'token' => $token]);
        } else {
            
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255','unique:users'],
                'phone' => ['required'],
                'gender' => ['required', 'string'],
                'city' => ['required', 'string'],
                'district' => ['required', 'string'],
                'pincode' => ['required', 'numeric'],
                'school' => ['required', 'string'],
                'password' => ['required'],
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
            $requestData = $request->all();
            $requestData['role'] = 6;
            unset($requestData['register_as_student']);
            $user =User::create($requestData);
            $token = JWTAuth::fromUser($user);
            return response()->json(['status'=>'success','user' => $user, 'token' => $token]);
           
        }
        
        
    }

   
}
