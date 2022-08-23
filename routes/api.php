<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DmController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);

// 投稿
Route::get('/post', [PostController::class, 'index']);
Route::post('/post', [PostController::class, 'post']);

// コメント
Route::get('/comment/{id}', [CommentController::class, 'index']);
Route::post('/comment/{id}', [CommentController::class, 'store']);
Route::put('/comment/{id}/edit', [CommentController::class, 'editPostData']);


// いいね
Route::post('/like', [LikeController::class, 'like']);

// profile
Route::get('/profile/{id}', [UserController::class, 'getProfile']);
Route::get('/profile/post/{id}', [UserController::class, 'getPostData']);
Route::post('/profile', [UserController::class, 'editProfile']);

// dm
Route::get('/dm', [DmController::class, 'search']);
Route::get('/dm/recent', [DmController::class, 'getRecentMessages']);
Route::get('/dm/message', [DmController::class, 'getDmMessage']);
Route::post('/dm/message', [DmController::class, 'insertDm']);
Route::post('/dm/room', [DmController::class, 'getRoomData']);

// follow
Route::post('/follow', [FollowController::class, 'followUser']);
Route::get('/following', [FollowController::class, 'following']);
Route::get('/follow/confirm', [FollowController::class, 'confirmFollowing']);

// 検索
Route::get('/search', [PostController::class, 'search']);
