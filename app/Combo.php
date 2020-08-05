<?php

namespace App;

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
        return $this->belongsTo('App\User');
    }
    public function properties()
    {
        return $this->hasMany('App\ComboProperties');
    }
}
