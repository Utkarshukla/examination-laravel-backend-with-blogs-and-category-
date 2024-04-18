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
        if($request->register_as_student ==true){
            $validateData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone'=>['required'],
                'father'=>['required','string', 'max:255'],
                'mother'=>['required','string', 'max:255'],
                'class' =>['required', 'number'],
                'dob'=>['required', 'date'],
                'city'=>['required', 'date'],
                'district'=>['required', 'date'],
                'pincode'=>['required', 'date'],
                'school'=>['required', 'date'],
                'password' => ['required'],
            ]);
            // $user =User::create($validateData);
            // $token = JWTAuth::fromUser($user);
            // return response()->json(['status'=>'success','user' => $user, 'token' => $token]);
            return response()->json(['data'=>$validateData]);
        } 
        if($request->register_as_student ==false){
            $validateData=$request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone'=>['required'],
                'gender'=>['required', 'string'],
                'city'=>['required', 'date'],
                'district'=>['required', 'date'],
                'pincode'=>['required', 'date'],
                'school'=>['required', 'date'],
                'password' => ['required'],
            ]);
            $validateData['role'] = 6;
             // $user =User::create($validateData);
            // $token = JWTAuth::fromUser($user);
            // return response()->json(['status'=>'success','user' => $user, 'token' => $token]);
            return response()->json(['data'=>$validateData]);
        }
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    }

    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required'],
    //     ]);
    // }

    // protected function createUser(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }
}
