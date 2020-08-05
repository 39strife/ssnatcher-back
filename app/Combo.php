<?php

namespace App;

use Actuallymab\LaravelComment\Contracts;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'combo',
    ];
    protected $with = ['user', 'properties'];
    protected $hidden = [
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function properties()
    {
        return $this->hasMany(ComboProperties::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable',)->whereNull('parent_id');
    }
}
