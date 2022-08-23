<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = array('id');
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function postImage()
    {
        return $this->hasMany('App\Models\PostImage');
    }
    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    //いいね機能
    public function likes(){
        return $this->hasMany('App\Models\Like');
    }

    public function isLike(?User $user): bool
    {
        return $user
            ? (bool)$this->likes->where('id', $user->id)->count()
            : false;
    }
}
