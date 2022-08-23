<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        $user = $request->user_id;
        $post_id = $request->post_id;
        $data = Like::where('post_id', $post_id)->where('user_id', $user)->exists();
        //データがなかったら保存
        if (!$data) {
            $like = new Like();
            $like->user_id = $request->user_id;
            $like->post_id = $request->post_id;
            $like->save();
        } else {
            Like::where('post_id', $post_id)->where('user_id', $user)->delete();
        }
    }
}
