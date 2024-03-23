<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class UserController extends Controller
{
    public function getFollowedUsersPosts()
{
    // Get the current user's id
    $userId = auth()->user()->id;

    // Get the ids of the users that the current user follows
    $followedUsers = DB::table('follower_user')->where('follower_id', $userId)->pluck('user_id');

    // Get the posts from the users that the current user follows
    $posts = DB::table('posts')
        ->join('users', 'users.id', '=', 'posts.user_id')
        ->whereIn('posts.user_id', $followedUsers)
        ->select('posts.*', 'users.name as user_name')
        ->get();

    return response()->json($posts);
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
                'name' => 'required',
                'email' => 'required|email|string'
            ]);
        $user = User::find(auth()->user()->id);
        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
        } else {
            return response()->json([
                'status' => false,
                'message' => error('')
            ], 500);
        }
}
}
}
