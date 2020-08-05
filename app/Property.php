<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    //
    protected $fillable = [
        'name'
    ];
    protected $hidden = [
        'game_id'
    ];

    public function game()
    {
        return $this->belongsTo("App\Game");
    }
}
