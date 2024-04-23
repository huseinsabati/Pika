<?php

namespace App\Http\Controllers;

use App\Models\post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\error;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getFollowedUsersPosts()
{
    // Get the current user's id
    $userId = auth()->user()->id;

    // Get the ids of the users that the current user follows
    $followedUsers = DB::table('follower_user')->where('follower_id', $userId)->pluck('user_id');

    return response()->json([
        'posts' => post::orderBy('created_at','desc')->with('user:id,name,profile')

                       ->with('like', function($like){
                   return $like->where('user_id', auth()->user()->id)
                       ->select('id','user_id','post_id')->get();
                       })->join('users', 'users.id', '=', 'posts.user_id')
                       ->whereIn('posts.user_id', $followedUsers)
                       ->select('posts.*', 'users.name as user_name')->with(['user.followers' => function ($followers) {
                        $loggedInUserId = auth()->user()->id;
                        $followers->where('follower_id', $loggedInUserId)->select('follower_id');
                        }])->withcount('comment','like')
                       ->get(),
            'status' => true
                       ], 200
    );
}

public function Profile(){
    $user = auth()->user();
    return response()->json([
        'User'=> $user
    ]);
}

public function update(Request $request){
    if(auth()->user()){
        $validateUser = Validator::make($request->all(),
            [
                'name' => 'nullable',
                'email' => 'nullable|email|string',
                'profile' => 'nullable|image',
                'cover' => 'nullable|image'
            ]);
        $user = User::find(auth()->user()->id);
        if ($request->filled('name')) {
        $user->update([
            'name' => $request->name,


        ]);
    }
        if ($request->filled('email')) {
            $user->update([
                'email' => $request->email

            ]);
        }

        if ($request->hasFile('profile')) {
            $user->update([
                'profile' => $request->file('profile')->store('Profileimage','public')

            ]);
        }
        elseif ($request->hasFile('cover')) {
            $user->update([
                'cover' => $request->file('cover')->store('coverimage','public')

            ]);
        }


        return response()->json([
            'status' => true,
            'message' => error('')
            ], 200);


}
}
public function notification(Request $request){
    return response()->json([
        'notifications' => DB::table('notifications')
                           ->select('data')
                           ->where('notifiable_id', auth()->user()->id)->get()
        ]);
}

}
