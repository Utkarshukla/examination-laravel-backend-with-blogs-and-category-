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

class AdminController extends Controller
{
    public function profile(){

    }
    public function generateHallTicket($olympiadId) {
        $olympiadStartDate = Olympiad::where('id', $olympiadId)->value('start_date');
        $formattedDate = Carbon::parse($olympiadStartDate)->format('Ymd');
        
        // Retrieve and update the count value
        $ticketCount = TicketCount::where('olympiad_id', $olympiadId)->firstOrFail();
        $newCount = $ticketCount->count + 1;
        $ticketCount->count = $newCount;
        $ticketCount->save();
        
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
    
    public function singlehallticket(Request $request, $id){
        $oid=$id;
        $startdate= Olympiad::select(['start_date'])->find($oid);
        $count= TicketCount::find(['count','current']);
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
