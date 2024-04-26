<?php

namespace App\Http\Controllers;

use App\Models\FrontendMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendMediaController extends Controller
{
    public function store(Request $req){
        $validateData=$req->validate([
            'name'=>'required | string',
            'author_id'=>'required',
            'media'=>'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mov|max:4096',
            'extra'=>'nullable',
        ]);
        if($req->hasFile('media')){
            $media =$req->file('media');
            $mediaPath = $media->store('frontendmedia','public');
            $validateData['media']=$mediaPath;
        }
        $frontendmedia= FrontendMedia::create($validateData);
        return response()->json(['status'=>'success','data'=>'media added Successfully']);
    }
    public function show(){
        $media =FrontendMedia::paginate(10);
        
        return response()->json(['status'=>'success','data'=>$media]);
    }
    public function find($page){
        $media= FrontendMedia::where('extra','=',$page)->get();
        return response()->json(['status'=>'success','data'=>$media]);
    }
}
