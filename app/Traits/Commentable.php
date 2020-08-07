<?php

namespace App\Traits;

use App\Comment;

trait Commentable
{
  // 
  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
  }

  public function comment($commentBody)
  {
    $user = auth("api")->user();
    $comment = new Comment(['comment' => $commentBody]);
    $comment->user()->associate($user);
    if ($this->comments()->save($comment)) {
      return [['message' => "Yay, your voice is now heard"], 200];
    }
    return [['message' => "Wow looks like there's been a mistake"], 200];
  }
}
