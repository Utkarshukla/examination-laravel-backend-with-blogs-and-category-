<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
use App\Models\Subject;
use App\Models\TicketCount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
class OlympiadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $currentDateTime = Carbon::now();

    return Olympiad::where('start_date', '>', $currentDateTime) 
                   ->orderBy('start_date', 'asc') 
                   ->paginate(10); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
         $user_id =$user->id;
         $user_role=$user->role;
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'status' => ['required', 'boolean'],
            'registration_deadline' => ['required', 'date'], // Fixed typo here
            'subject' => ['required', 'array', 'min:1'], 
            'subject.*.subject' => ['required', 'string'],
            'subject.*.subject_class' => ['required', 'string'],
            'subject.*.subject_fee' => ['required', 'numeric'],
            'subject.*.subject_marks' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        if($user_role !=1){
            return response()->json(['error'=>"You don't have access to create Olypiad"],422);
        }
        $olympiad = Olympiad::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'registration_deadline'=>$request->input('registration_deadline'),
            'author_id' => $user_id,
        ]);
    
        foreach ($request->input('subject') as $subjectData) {
            Subject::create([
                'olympiad_id' => $olympiad->id,
                'subject' => $subjectData['subject'],
                'subject_class' => $subjectData['subject_class'],
                'subject_fee' => $subjectData['subject_fee'],
                'subject_marks' => $subjectData['subject_marks']
            ]);
        }
        $hallintial= TicketCount::create([
            'count'=>0,
            'olympiad_id'=>$olympiad->id,
        ]);
        return response()->json(['status' => 'success','data'=>'data created successfully'], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $olympiad = Olympiad::with('subjects')->find($id);
        return response()->json(['status' => 'success','data'=>$olympiad]);
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
    public function update(Request $request, string $id)
    {
        $olympiad = Olympiad::find($id);
        $user = JWTAuth::parseToken()->authenticate();
         $user_id =$user->id;
         $user_role=$user->role;

        if (!$olympiad) {
            return response()->json(['error' => 'Olympiad not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['string'],
            'description' => ['string'],
            'start_date' => ['date'],
            'end_date' => ['date'],
            'status' => ['boolean'],
            'registration_deadline' => ['date'], // Fixed typo here
            //'author_id' => ['exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $olympiad->fill($request->all())->save();

        // Update associated subjects
        foreach ($request->input('subject') as $subjectData) {
            // Find the subject by its ID
            $subject = Subject::find($subjectData['id']);

            // If subject doesn't exist, skip
            if (!$subject) {
                continue;
            }

            // Validate request data for Subject
            $subjectValidator = Validator::make($subjectData, [
                'subject' => ['string'],
                'subject_class' => ['string'],
                'subject_fee' => ['numeric'],
                'subject_marks'=>['numeric']
            ]);

            if ($subjectValidator->fails()) {
                return response()->json(['error' => $subjectValidator->errors()], 422);
            }

            // Update subject details
            $subject->fill($subjectData)->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Olympiad and associated subjects updated successfully', 'data' => $olympiad]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $olympiad = Olympiad::find($id);

        if (!$olympiad) {
            return response()->json(['error' => 'Olympiad not found'], 404);
        }

        Subject::where('olympiad_id', $olympiad->id)->delete();

        $olympiad->delete();

        return response()->json(['status' => 'success', 'message' => 'Olympiad deleted successfully']);
    }
}
