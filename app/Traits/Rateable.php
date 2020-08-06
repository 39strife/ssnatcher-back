<?php

namespace App\Traits;

use App\Rating;

trait Rateable
{

  public function ratings()
  {
    return $this->morphMany(Rating::class, 'rateable');
  }

  public function getRatingForUser()
  {
    $id = auth("api")->id();
    return Rating::query()
      ->where('rateable_type', '=', get_class($this))
      ->where('rateable_id', '=', $this->id)
      ->where('user_id', '=', $id)
      ->first();
  }

  public function rate($newRating = false)
  {
    $id =  auth("api")->id();
    if (!$id) {
      return [["message" => "Hold on there bud! You need to log in!"], 401];
    }
    $rating = $this->getRatingForUser();
    if ($rating) {
      if ($rating->rating == $newRating) {
        $rating->delete();
        return [['message' => "Rating removed!"], 200];
      }
      $rating->rating = $newRating;
      $rating->save();
      return [['message' => "Rating udpated!"], 200];
    } else {
      $rating = new Rating();
      $rating->rating = $newRating;
      $rating->user_id = $id;
      $this->ratings()->save($rating);
      return [['message' => "Rating added!"], 200];
    }
  }

  public function hasUserRated()
  {
    $rating = $this->getRatingForUser();
    error_log(json_encode($rating));
    $return = [
      'rated' => false,
      'rating' => null,
    ];
    if ($rating) {
      $return = [
        'rated' => true,
        'rating' => $rating->rating,
      ];
    };
    $this->userRating = $return;
    return $this;
  }

  public function avarageRating()
  {
    $this->ratingCount = $this->ratings()->where('rating', 1)->count();
    return $this;
  }
}
