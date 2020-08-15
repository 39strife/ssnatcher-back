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

    public function getSocialsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
