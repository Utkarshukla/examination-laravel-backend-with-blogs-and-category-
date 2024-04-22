<?php

namespace App\Http\Controllers;

use App\Models\ParticipantSubject;
use App\Models\Participate;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
class ParticipateController extends Controller
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
    public function create(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id =$user->id;
        $school_id= $user->school_id;
        $validator = Validator::make($request->all(),[
            'olympiad_id'=>['required'],
            'subjects'=>['required']
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }
        $subjcet_Data=[];
         
        foreach($request->input('subjects') as $subject){
            $subject = Subject::find($subject);
            if ($subject) {
                $subjcet_Data[] = $subject;
            }
        }
       
        $totalMarks = 0;
        $totalFee = 0;
        foreach ($subjcet_Data as $subject) {
            $totalMarks += $subject->subject_fee;
            $totalFee += $subject->subject_fee;
        }

        $class=User::select('class','aadhar_number')->find($user_id);
        $requestData=$request->all();
        $requestData['class']=$class->class;
        $requestData['aadhar_number']=$class->aadhar_number;
        $requestData['total_marks']=$totalMarks;
        $requestData['total_fee']=$totalFee;
        $participates=Participate::create([
            'user_id'=>$user_id,
            'school_id'=>$school_id,
            'olympiad_id'=>$requestData['olympiad_id'],
            'aadhar_number'=>$requestData['aadhar_number'],
            'class'=>$requestData['class'],
            'total_amount'=>$requestData['total_fee'],
            'total_marks'=>$requestData['total_marks'],
            'created_by'=>$user_id
        ]);
        foreach ($request->input('subjects') as $subjectData) {
            ParticipantSubject::create([
                'participant_id' => $participates->id,
                'student_id' => $user_id,
                'subject_id' => $subjectData
            ]);
        }
        return response()->json(['status'=>'success','data'=>$participates,'message'=>'data added successfully']);
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
    public function show(Participate $participate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Participate $participate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Participate $participate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participate $participate)
    {
        //
    }
}
