<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Like;
use App\Models\User;

class PostController extends Controller
{
    // トップ画面の投稿の検索
    public function index(Request $request)
    {
        // フォローしているユーザーの情報取得
        $followUser = User::where('id', $request->following_user_id)->with('followings')->first()->followings;
        // フォローしているユーザーのidとログインユーザーのidを取得
        $id = [];
        array_push($id, $request->following_user_id);
        foreach ($followUser as $userData) {
            array_push($id, $userData->id);
        }
        //取得したidで検索
        $posts = Post::whereIn('user_id', $id)->with('postImage')->with('user')->get();
        if ($request->search) {
            $posts = Post::where("content", 'like', "%$request->search%")->with('postImage')->with('user')->get();
        }
        // いいねのカウントと判別
        foreach ($posts as $post) {
            $post->like_count = $post->likes()->count();
            $data = Like::where('post_id', $post->id)
                ->where('user_id', $request->user_id)
                ->exists();
            if ($data) {
                $post->isLike = true;
            } else {
                $post->isLike = false;
            }
        }
        return $posts;
    }
    // 投稿機能
    public function post(Request $request)
    {
        $data = Post::create($request->all());
        if ($request->image_path) {
            // ファイル名に登録時間を入れて、ユニークなファイル名に変更
            $file_name = time() . '.' . $request->image_path->getClientOriginalName();
            $request->image_path->storeAs('public', $file_name);
            $postImage = new PostImage();
            //postテーブルのidを取得、post_imageに保存
            $postImage->post_id = $data->id;
            $postImage->image_path = 'storage/' . $file_name;
            $postImage->save();
        }
        return $request->all();
    }
    // 検索機能
    public function search(Request $request)
    {
        // 投稿検索
        $posts = Post::where("content", 'like', "%$request->search%")->with('postImage')->with('user')->get();
        foreach ($posts as $post) {
            $post->like_count = $post->likes()->count();
            $data = Like::where('post_id', $post->id)
                ->where('user_id', $request->user_id)
                ->exists();
            if ($data) {
                $post->isLike = true;
            } else {
                $post->isLike = false;
            }
        }
        return $posts;
    }
}
