<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //
    protected $fillable = [
        'rating'
    ];

    protected $hidden = [
        'rateable_id', 'rateable_type'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function avarage()
    {
    }
}
