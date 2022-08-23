<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Post;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);
        if (Auth::guard()->attempt($credentials)) {
            $request->session()->regenerate();
            return new JsonResponse(['message' => 'ログインしました']);
        }
        return 'ログインに失敗しました。再度お試しください';
    }
    public function logout(Request $request): JsonResponse
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return new JsonResponse(['message' => 'ログアウトしました']);
    }
    public function register(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return new JsonResponse(['message' => '登録しました', 'newUser' => ['email' => $request->email, 'password' => $request->password]]);
    }

    public function editProfile(Request $request)
    {
        //アイコン画像
        $user = User::firstOrNew(['id' => $request->user_id]);
        if ($request->profile_img_path) {
            $profile_file_name = time() . '.' . $request->profile_img_path->getClientOriginalName();
            $request->profile_img_path->storeAs('public', $profile_file_name);
            //id一致したら上書き
            $user->profile_img_path = 'storage/' . $profile_file_name;
            // $user->save();
        }
        //カバー画像
        if ($request->cover_img_path) {
            $cover_file_name = time() . '.' . $request->cover_img_path->getClientOriginalName();
            $request->cover_img_path->storeAs('public', $cover_file_name);
            //id一致したら上書き
            $user->cover_img_path = 'storage/' . $cover_file_name;
            // $user->save();
        }
        if ($request->description) {
            $description = $request->description;
            $user->description = $description;
        }
        if ($request->name) {
            $name = $request->name;
            $user->name = $name;
        }
        $user->save();
        return $request->all();
    }
    public function getProfile($id)
    {
        return User::find($id);
    }
    public function getPostData($id)
    {
        return Post::where('user_id', $id)->with('postImage')->get();
    }
}
