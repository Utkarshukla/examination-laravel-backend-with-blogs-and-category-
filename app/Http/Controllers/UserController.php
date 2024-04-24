<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id=$user->id;
        $profile=User::with('userSchool')->findOrFail($user_id);
        return response()->json(['status'=>'success','data'=>$profile]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id=$user->id;
        $profile=User::findOrFail($user_id);
        $validator=Validator::make($request->all(),[
            'name'=>['required','string','max:255'],
            'email'=>['required','string','unique:users,email'],
            'phone'=>['required', 'numeric'],
            'father'=>['required','string'],
            'mother'=>['required','string'],
            'class'=>['required','numeric'],
            'gender'=>['required'],
            'dob'=>['required'],
            'city'=>['required', 'string'],
            'district'=>['required', 'string'],
            'state'=>['required','string'],
            'pincode'=>['required', 'string'],
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }
        $data=$request->all();
    
        $profile->update($data);
        return response()->json(['status'=>'success','data'=>$profile]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
