<?php

namespace App\Http\Controllers;

use notify;
use App\Models\like;
use App\Models\post;
use Illuminate\Http\Request;
use App\Notifications\LikeNotification;
use Illuminate\Support\Facades\Notification;

class LikeController extends Controller
{
    //
    public function likeorunlike($id){
        $post = post::find($id);
        if(!$post){
            return response([
                'message' => 'No Posts Found!!'

            ],403);
        }
        $like = $post->like()->where('user_id',auth()->user()->id)->first();
        $user = auth()->user();
        if(!$like){
            like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);
            $post->user->notify(new LikeNotification($user));
            return response([
                'message' => 'Liked!!'

            ],200);
        }

        else{
            $like->delete();
            return response([
                'message' => 'Unliked!!'

            ],200);
        }
    }
}
