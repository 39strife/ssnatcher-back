<?php

namespace App;

use Actuallymab\LaravelComment\Contracts;
use App\Traits\Commentable;
use App\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    use Rateable, Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'combo', 'description',
    ];
    protected $with = ['user', 'properties'];
    protected $hidden = [
        'user_id', 'character_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function character()
    {
        return $this->belongsTo(Character::class);
    }
    public function properties()
    {
        return $this->hasMany(ComboProperties::class);
    }
}
