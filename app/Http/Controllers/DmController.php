<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;


class DmController extends Controller
{
    public function search(Request $request)
    {
        // dmのユーザー検索
        // ログイン中のユーザー弾く
        return User::where([["name", 'like', "%$request->search%"], ['id', '!=', $request->login_user_id]])->get();
    }
    public function getRoomData(Request $request)
    {
        // チャットルーム検索
        $room1 = ChatRoom::where("user1_id", $request->user1_id)->where("user2_id", $request->user2_id);
        $room2 = ChatRoom::where("user1_id", $request->user2_id)->where("user2_id", $request->user1_id);
        // チャットルームが存在しなかったら作成
        if (!$room1->exists() && !$room2->exists()) {
            $param = [
                'user1_id' => $request->user1_id,
                'user2_id' => $request->user2_id,
            ];
            $room = ChatRoom::create($param);
            $data = [
                'chatData' => ChatMessage::where('chat_room_id', $room->id)->get(),
                'userData' => User::find($request->user2_id),
                'room_id' => $room->id,
            ];
            return $data;
        } else if ($room1->exists()) {
            $id = $room1->first()->id;
            $data = [
                // チャットのデータ検索
                'chatData' => ChatMessage::where('chat_room_id', $id)->get(),
                // チャットあいてのデータ検索
                'userData' => User::find($request->user2_id),
                // ルームが存在したらルームのidを送り出す
                'room_id' => $id,
            ];
            return $data;
        } else if ($room2->exists()) {
            $id = $room2->first()->id;
            $data = [
                // チャットのデータ検索
                'chatData' => ChatMessage::where('chat_room_id', $id)->get(),
                // チャットあいてのデータ検索
                'userData' => User::find($request->user2_id),
                // ルームが存在したらルームのidを送り出す
                'room_id' => $id,
            ];
            return $data;
        }
        return;
    }
    public function getDmMessage(Request $request)
    {
        // チャットルームを検索し、結びついているメッセージを取得
        return ChatRoom::find($request->chat_room_id)->getMessage;
    }
    public function insertDm(Request $request)
    {
        $data = [
            'chat_room_id' => $request->chat_room_id,
            'message' => $request->message,
            'user_id' => $request->user_id,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s.v'),
        ];
        ChatMessage::create($data);
        return ChatMessage::where("chat_room_id", $request->chat_room_id)->get();
    }
    public function getRecentMessages(Request $request)
    {
        $id = [];
        // 現在ログインしているユーザーが関係しているルームidを取得
        $room1 = User::where('id', $request->login_user_id)->with('room1')->first()->room1;
        foreach ($room1 as $roomData) {
            array_push($id, $roomData->id);
        }
        $room2 = User::where('id', $request->login_user_id)->with('room2')->first()->room2;
        foreach ($room2 as $roomData) {
            array_push($id, $roomData->id);
        }
        // idで検索をし、一番最新のメッセージを取得
        return ChatRoom::whereIn('id', $id)->with('getLatestMessage')->get();
    }
}
