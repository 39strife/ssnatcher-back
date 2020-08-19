<?php

namespace App\Traits;

trait Imageable
{
  // 
  public function getImageAttribute($value)
  {
    $beginning = "//" . $_SERVER['HTTP_HOST'] . "/";
    if (!$value) {
      return $beginning . "storage/uploads/defaults/default.jpg";
    }
    return $beginning . $value;
  }
}
