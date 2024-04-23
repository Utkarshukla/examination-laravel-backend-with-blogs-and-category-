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
            $totalMarks += $subject->subject_marks;
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
    public function show(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id =$user->id;
        $validator = Validator::make($request->all(),[
            'olympiad_id'=>['required','numeric'],
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }
        $data=$request->all();
        $data['user_id']= $user_id;
        $participatesData=Participate::where('olympiad_id', $data['olympiad_id'])
                            ->where('user_id', $data['user_id'])
                            ->firstOrFail(); 
        if($participatesData->total_ammount_locked == false && $participatesData->isfullPaid == false){
            return response()->json(['message'=>'payment ammount not locked and not paid','data'=>$participatesData]);
        } else if($participatesData->total_ammount_locked== true && $participatesData->isfullPaid == false) {
            return response()->json(['message'=>'payment ammount locked , now make payment','data'=>$participatesData]);
        } else{
            return response()->json(['message'=>'all done, wait for admit card, exam info and certification','data'=>$participatesData]);
        }

    }

    public function lock_register(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id=$user->id;
        $validator = Validator::make($request->all(),[
            'olympiad_id'=>['required','numeric'],
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }
        $data=$request->all();
        $data['user_id']= $user_id;
        $participate = Participate::where('olympiad_id', $data['olympiad_id'])
                               ->where('user_id', $data['user_id'])
                               ->first(); // or findOrFail() if it's guaranteed to exist

        if($participate) {
            $participate->update([
                'total_ammount_locked' => true
            ]);

            return response()->json(['status' => 'success', 'data' => 'Payment amount locked. Proceeded for Checkout'], 201);
        } else {
            return response()->json(['error' => 'Participation record not found'], 404);
        }
        
    }

    public function makepayment(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id=$user->id;
        $validator = Validator::make($request->all(),[
            'olympiad_id'=>['required','numeric'],
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }
        $data=$request->all();
        $data['user_id']= $user_id;
        $participate = Participate::where('olympiad_id', $data['olympiad_id'])
                               ->where('user_id', $data['user_id'])
                               ->firstOrFail(); 
        $price = $participate->total_amount;
        $id= $participate->id;
        //payment gateway api
        //if(payment=="success"){
            $participate->update([
               'isfullPaid'=>true,
            ]);
            //update payemt tabler
        //}
        return response()->json(['status'=>'success','message'=>'Payment done']);

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
