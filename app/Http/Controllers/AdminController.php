<?php

namespace App\Http\Controllers;

use App\Jobs\SendHallTicketEmail;
use App\Mail\HallTicket;
use App\Models\Olympiad;
use App\Models\Participate;
use App\Models\TicketCount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function profile(){

    }

    public function generateCertificate($participant)
{
    $backgroundImageUrl = public_path('storage/certificates/template.jpeg');
    $certificateContent = "<div style='position: relative;'>";
    $certificateContent .= "<img src='data:image/jpeg;base64,%BACKGROUND_IMAGE_ENCODED%' style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;' />";
    $certificateContent .= "<h1 style='text-align: center;'>Certificate of Participation</h1>";
    $certificateContent .= "<p style='text-align: center;'>This certifies that {$participant->participantUser->name} participated in the Olympiad.</p>";
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
                    $tickets[] = "Participant Email: $participantEmail, Hall Ticket: $hallTicket<br>";
                }
            });

    return response()->json(['status' => 'success', 'message' => $tickets]);
    }
    public function certificates(Request $request, $id){
        $olympiadId = $id;
        $tickets = [];
        $delay = 10;
        Participate::with('participantUser')
            ->where('olympiad_id', $olympiadId)
            ->whereNotNull('hall_ticket_no') 
            ->whereNull('certificate_url')
            ->chunk(20, function ($data) {
                    foreach ($data as $d) {
                        // Generate certificate and obtain its URL (replace this with your actual certificate generation logic)
                        $certificateUrl = $this->generateCertificate($d);

                        // Update the certificate_url field in the Participate model
                        $d->update(['certificate_url' => $certificateUrl]);
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
    

}
