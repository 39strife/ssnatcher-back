<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    //
    protected $fillable = ['name', 'image', 'description'];
    protected $hidden = ['game_id'];
    protected $with = ['game'];
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
