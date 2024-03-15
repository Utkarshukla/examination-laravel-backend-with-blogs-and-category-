<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postid) {
        $validateData = $request->validate([
            'comment' => 'required|string'
        ]);
        
        $authorId = Auth::id();
        
        $comment = Comment::create([
            'author_id' => $authorId,
            'post_id' => $postid,
            'comment' => $validateData['comment']
        ]);
        
        return response()->json(['status' => 'success','message'=>'Comment created successfully', 'comment' => $comment], 201);
    }
    public function show($post){
        $list=Comment::select('comments.*', 'users.name', 'users.email')
            ->join('users', 'comments.author_id', '=', 'users.id')
            ->where('comments.post_id', $post)
            ->get();
        return response()->json(['status'=>'success','data'=>$list]);
    }
}
