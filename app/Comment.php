<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = [
        'comment', 'rating'
    ];

    protected $hidden = [
        'user_id', 'parent_id', 'commentable_id', 'commentable_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo("commentable");
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
