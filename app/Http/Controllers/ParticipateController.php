<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
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
        $user_id = $user->id;
        $school_id = $user->school_id;
        $validator = Validator::make($request->all(), [
            'olympiad_id' => ['required'],
            'subjects' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        //already registered validation 
        $requestData0 = $request->all();
        $oid = $requestData0['olympiad_id'];
        
        $check = Participate::with('participantSubject')->where('olympiad_id',$oid)->where('user_id',$user_id)->get();
        if (!$check->isEmpty()) {
            $subjectPrice = [];
            $subjectMarks = [];
        
            // Iterate over existing participant subjects
            foreach ($check as $participant) {
                $participateId = $participant->id;
                foreach ($participant->participantSubject as $subject) {
                    $subjectdetail = Subject::find($subject->subject_id);
                    $subjectPrice[] = $subjectdetail->subject_fee;
                    $subjectMarks[] = $subjectdetail->subject_marks;
                }
            }
            $currentTotalAmount = array_sum($subjectPrice);
            $currentTotalMarks = array_sum($subjectMarks);
            $subjectIdsFrontend = $requestData0['subjects'];
            $subjectIdsBackend = $check->pluck('participantSubject.*.subject_id')->flatten()->toArray(); 
            $subjectsToAdd = array_diff($subjectIdsFrontend, $subjectIdsBackend);
            $newSubjectPrice = Subject::whereIn('id', $subjectsToAdd)->sum('subject_fee');
            $newSubjectMarks = Subject::whereIn('id', $subjectsToAdd)->sum('subject_marks');
            $newTotalAmount = $currentTotalAmount + $newSubjectPrice;
            $newTotalMarks = $currentTotalMarks + $newSubjectMarks;
            $updateRecord = Participate::findOrFail($participateId);
            $updateRecord->update([
                'total_amount' => $newTotalAmount,
                'total_marks' => $newTotalMarks,
            ]);
            foreach ($subjectsToAdd as $subjectId) {
                ParticipantSubject::updateOrCreate([
                    'participant_id' => $participateId,
                    'student_id' => $user_id,
                    'subject_id' => $subjectId
                ]);
            }
        
            return response()->json(['status' => 'success', 'message' => 'New subjects added successfully']);
        }
        foreach ($request->input('subjects') as $subject) {
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

        $class = User::select('class', 'aadhar_number')->find($user_id);
        $requestData = $request->all();
        $requestData['class'] = $class->class;
        $requestData['aadhar_number'] = $class->aadhar_number;
        $requestData['total_marks'] = $totalMarks;
        $requestData['total_fee'] = $totalFee;
        $participates = Participate::create([
            'user_id' => $user_id,
            'school_id' => $school_id,
            'olympiad_id' => $requestData['olympiad_id'],
            'aadhar_number' => $requestData['aadhar_number'],
            'class' => $requestData['class'],
            'total_amount' => $requestData['total_fee'],
            'total_marks' => $requestData['total_marks'],
            'created_by' => $user_id
        ]);
        foreach ($request->input('subjects') as $subjectData) {
            ParticipantSubject::create([
                'participant_id' => $participates->id,
                'student_id' => $user_id,
                'subject_id' => $subjectData
            ]);
        }
        return response()->json(['status' => 'success', 'data' => $participates, 'message' => 'data added successfully']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function showAll(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $user_role=$user->role;
        if($user_role == 5){
            $participatesData = Participate::with('participantOlympiad')
                                            ->select('olympiad_id')
                                            ->where('user_id', $user_id)
                                            ->groupBy('olympiad_id')
                                            ->paginate(10); // Paginate the results with 10 records per page
            return response()->json(['data' => $participatesData]);
        } 
        else if($user_role == 2){
            $participatesData = Participate::with('participantOlympiad')
                                            ->select('olympiad_id')
                                            ->where('created_by', $user_id)
                                            ->groupBy('olympiad_id')
                                            ->paginate(10); // Paginate the results with 10 records per page
            return response()->json(['data' => $participatesData]);
        }
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $user_role= $user->role;
        $oid = $id;
        $data = [];
        if ($user_role == 5) {
           
            $participatesData = Participate::with('participantUser')->where('user_id', $user_id)
                ->where('olympiad_id', $oid) 
                ->get();
            $olympiad = Olympiad::find($oid); // Find Olympiad by ID
            $data['participatesData'] = $participatesData;
            $data['olympiad'] = $olympiad;
            return response()->json(['data' => $data]);
        } else if ($user_role == 2) {
            $participatesData = Participate::with('participantUser')->where('created_by', $user_id)
                ->where('olympiad_id', $oid) 
                ->get();
            $totalAmount = $participatesData->where('isfullPaid', '!=', 1)->sum('total_amount');
            $olympiad = Olympiad::find($oid); 
            $data['participatesData'] = $participatesData;
            $data['olympiad'] = $olympiad;
            $data['totalAmount'] = $totalAmount;
            return response()->json(['data' => $data]);
        }
    }

    public function deleteOne(Request $request, string $oid , string $participate_id){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $created_by = $user->id;
            
            $participation = Participate::where('created_by', $created_by)
            ->where('id', $participate_id)
            ->where('olympiad_id', $oid)
            ->firstOrFail(); 
            if($participation->isfullPaid != 1){
                $participation->participantSubject()->delete();
                $participation->delete();
                return response()->json(['status'=>'failure','message'=>"Participation record deleted successfully"]);
            } else {
                return response()->json(['status'=>'failure','message'=>"Can't delete , Student already Paid "]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Record Not Found'], 404);
        }
    }

    public function lock_register(Request $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $oid = $id;
        $data = $request->all();
        $data['user_id'] = $user_id;
        $participate = Participate::where('olympiad_id', $oid)
            ->where('user_id', $user_id)
            ->first(); // or findOrFail() if it's guaranteed to exist

        if ($participate) {
            $participate->update([
                'total_ammount_locked' => true
            ]);

            return response()->json(['status' => 'success', 'data' => 'proceeded-to-checkout'], 201);
        } else {
            return response()->json(['error' => 'Participation record not found'], 404);
        }
    }

    // public function makepayment(Request $request, string $id)
    // {
    //     $user = JWTAuth::parseToken()->authenticate();
    //     $user_id = $user->id;
    //     $oid = $id;
    //     $participate = Participate::where('olympiad_id', $oid)
    //         ->where('user_id', $user_id)
    //         ->first(); // or findOrFail() if it's guaranteed to exist

    //     $price = $participate->total_amount;
    //     $participateid = $participate->id;
    //     //payment gateway api
    //     //if(payment=="success"){
    //     $participate->update([
    //         'isfullPaid' => true,
    //     ]);
    //     //update payemt tabler
    //     //}
    //     return response()->json(['status' => 'success', 'message' => 'Payment done']);
    // }

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
