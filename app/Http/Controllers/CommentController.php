<?php

namespace App\Http\Controllers;

use App\Models\post;
use App\Models\comment;
use Illuminate\Http\Request;
use App\Notifications\CommentNotification;
use Egulias\EmailValidator\Parser\Comment as ParserComment;

class CommentController extends Controller
{
    //
    public function index($id){
        $post = post::find($id);
        if(!$post)
        {
            return response([
                'message' => 'No Posts Found!!'
            ],403);
        }
        return response([
             'comment' => $post->comment()->with('user:id,name,image')->get()
        ], 200);
    }

    public function store(Request $request,$id){
        $post = post::find($id);
        if(!$post)
        {
            return response([
                'message' => 'No Posts Found!!'
            ],403);
        }
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);
        comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);
        $user= auth()->user();
        $post->user->notify(new CommentNotification($user));
        return response([
            'message' => 'Comment Created'
        ], 200);
    }

    public function update(Request $request, $id){
        $comment = comment::find($id);
        if(!$comment)
        {
            return response([
                'message' => 'No Comments Found!!'
            ],403);
        }
        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permision denied.'
            ],403);

        }
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);

        return response([
            'message' => 'Comment Updated Successfully',
            'comment' => $comment
        ], 200);
    }

    public function delete($id){
        $comment = comment::find($id);
        if(!$comment){
            return response([
                'message' => 'No comments Found!!'

            ],403);
        }
        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permision denied.'
            ],403);

        }
        $comment->delete();

        return response([
            'message' => 'Comment Deleted Successfully'
        ], 200);

    }
}
