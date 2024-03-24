<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//unprotected
Route::post("/register", [AuthController::class, 'register']);
Route::post("/login", [AuthController::class, 'login']);
//protected
Route::group(['middleware' =>['auth:sanctum']],function(){
    //user
    Route::get('/user', [UserController::class,'Profile']);//profile
    Route::post('/user/update', [UserController::class,'update']);//update profile
    Route::post('/logout', [AuthController::class,'logout']);

    //post
    Route::get('/post', [PostController::class,'index']);//show all posts
    Route::get('/feed', [UserController::class,'getFollowedUsersPosts']);//user feed
    Route::post('/post', [PostController::class,'store']);//create post
    Route::post('/post/{id}', [PostController::class,'update']);//update post
    Route::delete('/post/{id}', [PostController::class,'destroy']);//delete post


    //comment
    Route::get('/post/{id}/comment', [CommentController::class,'index']);//show all comment
    Route::post('/post/{id}/comment', [CommentController::class,'store']);//create comment
    Route::put('/comment/{id}', [CommentController::class,'update']);//update comment
    Route::delete('/comment/{id}', [CommentController::class,'destroy']);//delete comment
    //like
    Route::post('/post/{id}/like', [LikeController::class,'likeorunlike']);
    //Follow
    Route::post('/user/{id}/follow', [FollowerController::class,'FollowUnfollow']);
    //search
    Route::get('/search', [PostController::class,'search']);
    //get notifications
    Route::get('/notifications', [UserController::class,'notification']);
});
