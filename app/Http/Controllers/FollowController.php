<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;

class FollowController extends Controller
{
    // フォローボタン
    public function followUser(Request $request)
    {
        // フォローしているか検索
        $follow = Follow::where('followed_user_id', $request->followed_user_id)->where('following_user_id', $request->following_user_id);
        // してたら解除
        if ($follow->exists()) {
            $follow->delete();
            return;
        }
        return Follow::create($request->all());
    }
    // フォローしているかどうかの確認
    public function confirmFollowing(Request $request)
    {
        // フォローしているかどうか検索
        $follow = Follow::where('followed_user_id', $request->followed_user_id)->where('following_user_id', $request->following_user_id);
        if ($follow->exists()) {
            // フォローしてたらfalse
            return false;
        }
        return true;
    }
    // 
    public function following(Request $request)
    {
        if ($request->left_sidebar) {
            return User::where('id', $request->login_user_id)->with('followings')->first();
        } else {
            $followUser = User::where('id', $request->login_user_id)->with('followings')->first()->followings;
            $id = [];
            array_push($id, $request->login_user_id);
            foreach ($followUser as $userData) {
                array_push($id, $userData->id);
            }
            return User::whereNotIn('id', $id)->get();
            return $id;
            User::whereIn('id', '!=', $id)->get();
        }
        return User::with('followings')->get(['id']);
    }
}
