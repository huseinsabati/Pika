<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\FollowNotification;

class FollowerController extends Controller
{
    public function FollowUnfollow($id){
        $user = User::find($id);
        $check = $user->followers()->where('follower_id',auth()->user()->id)->first();
        if(!$check){
        $follower = auth()->user();
        $follower->following()->attach($id);
        $user->notify(new FollowNotification($follower));
        return response([
            'message' => 'Followed!!'

        ],200);
        }
        else{

            $follower = auth()->user();
            $follower->following()->detach($id);
            return response([
                'message' => 'Unfollowed!!'
            ],200);

        }


    }


}
