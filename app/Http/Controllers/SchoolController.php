<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return School::get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user=JWTAuth::parseToken()->authenticate();
        $user_id=$user->id;
        $user_role=$user->role;
        if($user_role != 1){
            return response()->json(['status'=>'failure','data'=>"you don't have access to create a shcool"]);
        }
        $validator = Validator::make($request->all(), [
            'school_name' => ['required', 'string', 'max:255'],
            'school_landmark' => ['required', 'string', 'max:255'],
            'school_email'=>['required','email','string', 'max:255'],
            'school_phone'=>['required','string'],
            'school_city' => ['required', 'string', 'max:255'],
            'school_district' => ['required', 'string', 'max:255'],
            'school_state' => ['required', 'string', 'max:255'],
            'school_unique_code' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>'failure','error'=> $validator->errors()], 422);
        }
        $data = $request->all();
        $data['author_id']=$user_id;
        $school = School::create($data);
        return response()->json(['status'=>'success','data'=>$school.' successfully added']);
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
        return School::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user=JWTAuth::parseToken()->authenticate();
        $user_id=$user->id;
        $validator = Validator::make($request->all(), [
            'id'=>['required','numeric'],
            'school_name' => ['required', 'string', 'max:255'],
            'school_email'=>['required','email','string', 'max:255'],
            'school_phone'=>['required','string'],
            'school_landmark' => ['required', 'string', 'max:255'],
            'school_city' => ['required', 'string', 'max:255'],
            'school_district' => ['required', 'string', 'max:255'],
            'school_state' => ['required', 'string', 'max:255'],
            'school_unique_code' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>'failure','error'=> $validator->errors()], 422);
        }
        $data = $request->all();
        $data['author_id']=$user_id;
        $school=School::find($request->id);
        $school->update([
            'school_name'=>$data['school_name'],
            "school_email"=>$data['school_email'],
            "school_phone"=>$data['school_phone'],
            "school_landmark"=>$data['school_landmark'],
            "school_city"=>$data['school_city'],
            "school_district"=>$data['school_district'],
            "school_state"=>$data['school_state'],
            "school_unique_code"=>$data['school_unique_code'],
            "author_id"=>$data['author_id']
        ]);
        
        return response()->json(['status'=>'success','data'=>$school.' successfully updated']);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        //
    }
}
