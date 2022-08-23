<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;
    protected $guarded = array('id');
    protected $with = ['user1', 'user2'];

    public function user1()
    {
        return $this->belongsTo('App\Models\User', 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo('App\Models\User', 'user2_id');
    }

    public function getMessage()
    {
        return $this->hasMany('App\Models\ChatMessage');
    }
    public function getLatestMessage()
    {
        return $this->hasOne('App\Models\ChatMessage')->latestOfMany();
    }
}
