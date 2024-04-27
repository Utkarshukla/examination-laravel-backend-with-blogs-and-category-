<?php

namespace App\Http\Controllers;

use App\Jobs\SendHallTicketEmail;
use App\Mail\HallTicket;
use App\Models\Olympiad;
use App\Models\ParticipantSubject;
use App\Models\Participate;
use App\Models\TicketCount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function profile(){

    }

    public function generateCertificate($participant)
{
    $certificateID = $participant->hall_ticket_no;
    $percentage = ($participant->obtain_marks / $participant->total_marks) * 100;
    $fatherName= $participant->participantUser->father;
    $date= $participant->participantOlympiad->start_date;
    $class=$participant->class;
    $olympiadname= $participant->participantOlympiad->name;
    $backgroundImageUrl = public_path('storage/template.jpeg');
    $certificateContent = "<div style='position: relative;'>";
    $certificateContent .= "<img src='data:image/jpeg;base64,%BACKGROUND_IMAGE_ENCODED%' style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;' />";
    $certificateContent .= "<h3 style='text-align: center;color:#4e5776;position: absolute;top:500; margin-left:20px;'>Certificate ID: {$certificateID}</h3>";
    $certificateContent .= "<h3 style='text-align: center;color:#4e5776;position: absolute;top:530; margin-left:20px;'>Marks Obtained: {$participant->obtain_marks} in total {$participant->total_marks} </h3>";
    $certificateContent .= "<h1 style='text-align: center;color:#4e5776;position: absolute;top:46%; left: 50%; transform: translate(-50%, -50%);'><center>{$participant->participantUser->name}</center></h1>";
    $certificateContent .= "<p style='text-align: center;color:#4e5776;position: absolute;top:380; padding-left:20px; padding-right:20px'>This is to certify that <b> {$participant->participantUser->name}</b> , son/daughter of <b>$fatherName</b>, has successfully participated in the <b>{$olympiadname}</b> held in <b> $date</b> . He/She is appeared in <b>class $class</b>. He/She has demonstrated exceptional talent and dedication, achieving a remarkable <b style='color:#4e5776;'> $percentage% </b> in the <b> {$olympiadname}</b> . His/Her commitment to academic excellence and passion for learning are truly commendable. We congratulate for outstanding performance and wish Him/Her continued success in his/her academic journey.</p>";
    $certificateContent .= "</div>";
    $backgroundImageData = file_get_contents($backgroundImageUrl);
    $backgroundImageEncoded = base64_encode($backgroundImageData);
    $certificateContent = str_replace('%BACKGROUND_IMAGE_ENCODED%', $backgroundImageEncoded, $certificateContent);
    $dompdf = new Dompdf();
    $dompdf->loadHtml($certificateContent);
    $dompdf->render();
    $filename = 'certificate_' . $participant->id . '_' . time() . '.pdf';
    $filePath = 'certificates/' . $filename;

    // Save PDF to storage
    Storage::disk('public')->put($filePath, $dompdf->output());

    // Generate URL for the saved certificate
    $certificateUrl = asset('storage/' . $filePath);

    return $certificateUrl;
}



    public function generateHallTicket($olympiadId) {
        $olympiad = Olympiad::with('ticketCount')->findOrFail($olympiadId);
        $olympiadStartDate = $olympiad->start_date;
        $formattedDate = Carbon::parse($olympiadStartDate)->format('Ymd');
        $newCount = $olympiad->ticketCount->count + 1;
        $olympiad->ticketCount->count = $newCount;
        $olympiad->ticketCount->save();
        $hallTicketNumber = $formattedDate . str_pad($newCount, 3, '0', STR_PAD_LEFT);
        return $hallTicketNumber;
    }
    public function hallticket(Request $request, $id) {
        $olympiadId = $id;
        $tickets = [];
        $delay = 10;
        Participate::with('participantUser')
            ->where('olympiad_id', $olympiadId)
            ->where('hall_ticket_no', null)
            ->chunk(20, function ($data) {  //->chunk(20, function ($data) use ($delay) {
                foreach ($data as $d) {
                    $hallTicket = $this->generateHallTicket($d->olympiad_id);
                    $d->update(['hall_ticket_no' => $hallTicket]);
                    dispatch(new SendHallTicketEmail($d));
                    $participantEmail = $d->participantUser->email; 
                    
                }
            });

    return response()->json(['status' => 'success', 'message' => $tickets]);
    }
    public function certificates(Request $request, $id){
        $olympiadId = $id;
        $tickets = [];
        $delay = 10;
        Participate::with('participantUser')->with('participantOlympiad')
            ->where('olympiad_id', $olympiadId)
            ->whereNotNull('hall_ticket_no') 
            ->whereNull('certificate_url')
            ->whereNotNull('obtain_marks')
            ->chunk(20, function ($data) {
                    foreach ($data as $d) {
                        $certificateUrl = $this->generateCertificate($d);
                        $d->update(['certificate_url' => $certificateUrl]);
                        $tomail = $d->participantUser->email;

                    }
                }
            );
    }
    public function olypiad_participates( string $id){
        $data= Participate::with('participantUser')->where('olympiad_id',$id)->get();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function olypiad_participate_single( string $id, string $user_id){
        $data= Participate::with('participantUser')->where('olympiad_id',$id)->where('user_id',$user_id)->get();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function alluser(){
        $data = User::where('id', '!=', 1)->get();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function singleUser(string $id){
        $data = User::with('userSchool')->where('id', '=', $id)->first();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function allincharge(){
        $data = User::where('role', '=',2)->get();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function singleIncharge(string $id){
        $data = User::with('userSchool')->where('id','=',$id)->first();
        if($data){
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        return response()->json(['status'=>'success','data'=>'not a incharge'],200);
    }
    public function pendingIncharge(){
        $data = User::with('userSchool')->where('role', '=',6)->get();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function approveIncharge(string $id){
        $data= User::where('id',$id)->firstOrFail();
        $data->update([
            'role'=>2
        ]);
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function unapproveIncharge(string $id){
        $data= User::where('id',$id)->firstOrFail();
        $data->update([
            'role'=>6
        ]);
        return response()->json(['status'=>'success','data'=>$data],200);
    }

    
    // public function inchargeMakeStudent(string $id){
    //     $data= User::where('id',$id)->firstOrFail();
    //     $data->update([
    //         'role'=>5
    //     ]);
    //     return response()->json(['status'=>'success','data'=>$data],200);
    // }
    

    public function getuploadmarkscsv(string $id){
        $data = Participate::with(['participantUser', 'participantSubject.subject'])->where('olympiad_id', $id)->get();
        $filteredData = $data->map(function ($item) {
            return [
                'participate_id'=>$item->id,
                'hall_ticket_no' => $item->hall_ticket_no,
                'aadhar_number' => $item->aadhar_number,
                'class' => $item->class,
                'total_marks' => $item->total_marks,
                'obtain_marks' => $item->obtain_marks,
                'participant_user' => [
                    'name' => $item->participantUser->name,
                ],
                'participant_subject' => $item->participantSubject->map(function ($subject) {
                    return [
                        'subject_id' =>$subject->subject->id,
                        'subject_name' => $subject->subject->subject,
                        'subject_class' => $subject->subject->subject_class,
                        'subject_marks'=> $subject->subject->subject_marks,
                    ];
                }),
            ];
        });
        $filteredArray = $filteredData->toArray();
        return response()->json(['status'=>'success','message'=>'Olypiads_marks_CSV','data'=>$filteredArray]);
    }
    public function postuploadmarkscsv(Request $request, string $id){
        $validator = Validator::make($request->all(), [
            '*.participate_id' => ['required'], // Participant ID
            '*.hall_ticket_no' => ['required'], // Hall ticket number
            '*.obtain_marks' => ['required'], // Total obtain marks
            '*.subject_marks' => ['required', 'array'], // Array of subject and marks [{64:80}, {63:80}]
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        Participate::where('olympiad_id', $id)->chunk(1000, function ($participates) use ($request) {
            foreach ($participates as $participate) {
                $data = collect($request->all())->where('participate_id', $participate->id)->first();
    
                if ($data) {
                    $participate->obtain_marks = $data['obtain_marks'];
                    $participate->save();
    
                    foreach ($data['subject_marks'] as $subjectMark) {
                        foreach ($subjectMark as $subjectId => $marks) {
                            ParticipantSubject::where('participant_id', $participate->id)
                                ->where('subject_id', $subjectId)
                                ->update(['obtain_marks' => $marks]);
                        }
                    }
                }
            }
        });
    
        return response()->json(['status' => 'success', 'message' => 'Marks updated successfully']);
    }
    

}




