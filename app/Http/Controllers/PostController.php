<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\post;
use PHPUnit\Framework\Constraint\IsEmpty;

class PostController extends Controller
{
    //
    public function index(){

        return response([

            'posts' => post::orderBy('created_at','desc')->with('user:id,name,profile')
                       ->withcount('comment','like')
                       ->with('like', function($like){
                return $like->where('user_id', auth()->user()->id)
                       ->select('id','user_id','post_id')->get();
                       })->get(),
            'status' => true
                       ], 200);

    }
    public function store(Request $request){

        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        $post = post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id
        ]);

        if($request->hasFile('image')){
            $post['image'] = $request->file('image')->store('postimage','public');
            $post->save();
        }
        return response([
            'message' => 'Post Created Successfully',
            'status' => true,
            'post' => $post
        ], 200);

    }
    public function update(Request $request, $id){

        $post = post::find($id);
        if(!$post){
            return response([
                'status' => false,
                'message' => 'No Posts Found!!'

            ],403);
        }
        if($post->user_id != auth()->user()->id){
            return response([
                'status' => false,
                'message' => 'Permision denied.'
            ],403);

        }

        $attrs = $request->validate([
            'body' => 'string',
            'image' => 'image'
        ]);
        if($request->filled('body')){

        $post->update([
            'body' => $attrs['body']
        ]);
    }
        if($request->hasFile('image')){
            $post->update([
            'image' => $request->file('image')->store('postimage','public')
            ]);
        }

        return response([
            'message' => 'Post Updated Successfully',
            'status' => true,
            'post' => $post
        ], 200);

    }
    public function destroy($id){
        $post = post::find($id);
        if(!$post){
            return response([
                'status' => false,
                'message' => 'No Posts Found!!'

            ],403);
        }
        if($post->user_id != auth()->user()->id){
            return response([
                'status' => false,
                'message' => 'Permision denied.'
            ],403);

        }

        $post->comment()->delete();
        $post->like()->delete();
        $post->delete();

        return response([
            'message' => 'Post Deleted Successfully',
            'status' => true
        ], 200);

    }
    public function search(Request $request)
    {
    $search = $request->get('search');
    $posts = Post::where('body', 'like', '%' . $search . '%')
             ->orWhereHas('user', function ($query) use ($search) {
             $query->where('name', 'like', '%' . $search . '%');
             })->get();
        if (!$posts->isEmpty()) {
        return response([
            'message' => 'Post found Successfully',
            'status' => true,
            'post' => $posts
        ], 200);
       }
       else {
        return response([
            'status' => false,
            'message' => 'No Posts Found'
        ], 200);
    }
    }

}
