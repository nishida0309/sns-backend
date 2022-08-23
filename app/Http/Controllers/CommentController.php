<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

class CommentController extends Controller
{
    public function index($id)
    {
        // コメント内でwithを使用している
        $post = Post::where("id", $id)->with("user")->with("postImage")->with("comments")->first();
        $post->like_count = $post->likes()->count();
        $data = Like::find($id);
        if ($data) {
            $post->isLike = true;
        } else {
            $post->isLike = false;
        }
        return $post;
    }
    //パラメータ$post_id
    public function store(Request $request, $id)
    {
        $comment = new Comment();
        $comment->post_id = $id;
        $comment->user_id = $request->user_id;
        $comment->comment = $request->comment;
        $comment->save();
    }
    public function editPostData(Request $request, $id)
    {
        // return "test";
        $update = [
            "content" => $request->content,
        ];
        Post::where('id', $id)->update($update);
        return $request->all();
        return Post::where('id', $id)->with('postImage')->first()->postImage;
    }
}
