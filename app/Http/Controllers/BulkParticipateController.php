<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class BulkParticipateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id =$user->id;
        $user_role=$user->role;
        if($user_role == 1 || $user_role ==2){
            $validator = Validator::make($request->all(),[
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'aadhar_number'=> ['required','numeric','digits:12'],
                'phone' => ['numeric'],
                'father' => ['string', 'max:255'],
                'mother' => ['string', 'max:255'],
                'class' => ['required', 'numeric'],
                'dob' => ['date'],
                'city' => ['string'],
                'district' => ['required', 'string'],
                'pincode' => ['required', 'numeric'],
                'school_unique_code' => ['required', 'numeric'],
                'olympiad_id'=>['required'],
                'subjects'=>['required']
            ]);
            return response()->json(['error'=>'You are  allowed to register via csv'],201);
        }
        return response()->json(['message'=>'You are not allowed to register via csv'],422);

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
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
