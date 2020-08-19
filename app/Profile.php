<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
    protected $fillable = [
        "avatar",
        "banner",
        "description",
        "socials",
    ];

    protected $hidden = [
        "user_id",
    ];

    public function getBannerAttribute($value)
    {
        $beginning = "//" . $_SERVER['HTTP_HOST'] . "/";
        if (!$value) {
            return $beginning . "storage/uploads/defaults/banner.jpg";
        }
        return $beginning . $value;
    }
    public function getSocialsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getAvatarAttribute($value)
    {
        $beginning = "//" . $_SERVER['HTTP_HOST'] . "/";
        if (!$value) {
            return $beginning . "storage/uploads/defaults/profile.jpg";
        }
        return $beginning . $value;
    }



    public function user()
    {
        $this->belongsTo(User::class);
    }
}
