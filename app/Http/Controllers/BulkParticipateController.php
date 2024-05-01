<?php

namespace App\Http\Controllers;

use App\Models\ParticipantSubject;
use App\Models\Participate;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
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
        $count = 0;
        $totalRequests = count($request->all());
        if($user_role == 1 || $user_role ==2){
            $count=0;
            foreach($request->all() as $requestdata){
                $validator = Validator::make($requestdata,[
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
                    'school_unique_code' => ['required'],
                    'olympiad_id'=>['required','numeric'],
                    'subjects'=>['required']
                ]);
                if($validator->fails()){
                    return response()->json(['error'=>$validator->errors()],422);
                }

                $name=$requestdata['name'];
                $email=$requestdata['email'];
                $aadhar=$requestdata['aadhar_number'];
                $phone= $requestdata['phone'];
                $father=$requestdata['father']??'NAN';
                $mother=$requestdata['mother'] ??'NAN';
                $class=$requestdata['class'];
                $dob=$requestdata['dob'];
                $city=$requestdata['city']?? 'Vijayawada';
                $district = $requestdata['district'] ?? 'Krishna';
                $state = $requestdata['state']??'Andhra Pradesh';
                $pincode =$requestdata['pincode']?? 520007;
                $school_code= $requestdata['school_unique_code'] ?? 'matrixmath';
                $olympiad_id=$requestdata['olympiad_id'];
                $subjects=$requestdata['subjects'];
      
                $school_id = School::where('school_unique_code', $school_code)->pluck('id')->first();
                if(!$school_id){
                    return response()->json(['error'=>'school not found with this unique code'],422);
                }
                
                $userfromTable=User::where('aadhar_number',$aadhar)->first();
                
                if($userfromTable){
                    $user = User::find($userfromTable->id);
                    $user->update([
                        'class'=>$class
                    ]);
                } else {
                    $user=User::create([
                        'name'=>$name,
                        'email'=>$email,
                        'aadhar_number'=>$aadhar,
                        'phone'=>$phone,
                        'father'=>$father,
                        'mother'=>$mother,
                        'class'=>$class,
                        'dob'=>$dob,
                        'city'=>$city,
                        'district'=>$district,
                        'pincode'=>$pincode,
                        'school_id'=>$school_id,
                        'state'=>$state,
                        'password'=>$aadhar,
                        'created_by'=>$user_id
                    ]);
                }
                $subjectData=[];
                foreach($subjects as $subject){
                    $subject= Subject::find($subject);
                    if($subject){
                        $subjectData[]=$subject;
                    }
                }
                $totalMarks =0;
                $totalFee=0;
                foreach($subjectData as $subject){
                    $totalMarks +=$subject->subject_marks;
                    $totalFee += $subject->subject_fee;
                }
                $participates=Participate::create([
                    'user_id'=>$user->id,
                    'school_id'=>$school_id,
                    'olympiad_id'=>$olympiad_id,
                    'aadhar_number'=>$aadhar,
                    'class'=>$class,
                    'total_amount'=>$totalFee,
                    'total_marks'=>$totalMarks,
                    'created_by'=>$user_id
                ]);
                foreach ($subjectData as $Data) {
                    //return response()->json(['data'=>$Data->id],201);
                    ParticipantSubject::create([
                        'participant_id' => $participates->id,
                        'student_id' => $user->id,
                        'subject_id' => $Data->id
                    ]);
                }
                 $count++ ;
                 
            }

            $totalRequests = count($request->all());
            $percentage = ($count / $totalRequests) * 100;
            return response()->json(['status'=>'success' ,'percentage' => $percentage, 'message' => $percentage.'%  Data uploaded successfully']);
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
    public function show(Request $request,string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id =$user->id;
        $olympiad_id=$id;
        $data= Participate::where('created_by',$user_id)->where('olympiad_id',$olympiad_id);
        return response()->json(['status'=>'success','data'=>$data]);
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
