<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

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
        $validator = Validator::make($request->all(), [
            'school_name' => ['required', 'string', 'max:255'],
            'school_landmark' => ['required', 'string', 'max:255'],
            'school_city' => ['required', 'string', 'max:255'],
            'school_district' => ['required', 'string', 'max:255'],
            'school_state' => ['required', 'string', 'max:255'],
            'school_unique_code' => ['required', 'string', 'max:255'],
            'author_id'=>['required','numeric']
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>'failure','error'=> $validator->errors()], 422);
        }
        $data = $request->all();
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
    public function update(Request $request, School $school)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        //
    }
}
