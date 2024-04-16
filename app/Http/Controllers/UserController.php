<?php

namespace App\Http\Controllers;

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

    // Get the posts from the users that the current user follows
    $posts = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->whereIn('posts.user_id', $followedUsers)
            ->select('posts.*', 'users.name as user_name')
            ->get();

    return response()->json([
        'posts' => $posts,
        'status' => true
    ]);
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
