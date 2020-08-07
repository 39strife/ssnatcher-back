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

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
