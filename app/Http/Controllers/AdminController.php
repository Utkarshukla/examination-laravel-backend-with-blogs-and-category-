<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
use App\Models\Participate;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function profile(){

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
        $data = User::where('id', '=', $id)->first();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function allincharge(){
        $data = User::where('role', '=',2)->get();
        return response()->json(['status'=>'success','data'=>$data],200);
    }
    public function singleIncharge(string $id){
        $data = User::where('role', '=',2)->where('id','=',$id)->first();
        if($data){
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        return response()->json(['status'=>'success','data'=>'not a incharge'],200);
    }
    public function pendingIncharge(){
        $data = User::where('role', '=',6)->get();
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
