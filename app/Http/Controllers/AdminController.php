<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
use App\Models\Participate;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function profile(){

    }

    public function olypiad_participates( string $id){
        $data= Participate::with('participantUser')->where('olympiad_id',$id)->get();
        return response()->json(['status'=>'success','data'=>$data]);
    }
    public function olypiad_participate_single( string $id, string $user_id){
        $data= Participate::with('participantUser')->where('olympiad_id',$id)->where('user_id',$user_id)->get();
        return response()->json(['status'=>'success','data'=>$data]);
    }
}
