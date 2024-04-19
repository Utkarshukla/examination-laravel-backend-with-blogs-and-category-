<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OlympiadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Olympiad::with('subjects')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'status' => ['required', 'boolean'],
            'registration_deadline' => ['required', 'date'], // Fixed typo here
            'author_id' => ['required', 'exists:users,id'], 
            'subject' => ['required', 'array', 'min:1'], 
            'subject.*.subject' => ['required', 'string'],
            'subject.*.subject_class' => ['required', 'string'],
            'subject.*.subject_fee' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        $olympiad = Olympiad::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'registration_deadline'=>$request->input('registration_deadline'),
            'author_id' => $request->input('author_id'),
        ]);
    
        // Create subjects associated with the olympiad
        foreach ($request->input('subject') as $subjectData) {
            Subject::create([
                'olympiad_id' => $olympiad->id,
                'subject' => $subjectData['subject'],
                'subject_class' => $subjectData['subject_class'],
                'subject_fee' => $subjectData['subject_fee'],
            ]);
        }
    
        return response()->json(['status' => 'success','data'=>'data created successfully'], 201);
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
    public function show(Olympiad $olympiad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Olympiad $olympiad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Olympiad $olympiad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Olympiad $olympiad)
    {
        //
    }
}
