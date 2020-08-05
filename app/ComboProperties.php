<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComboProperties extends Model
{
    //
    protected $fillable = [
        'value',
    ];

    protected $hidden = [
        'combo_id', 'property_id'
    ];

    protected $with = ['name'];


    public function name()
    {
        return $this->belongsTo('App\Property', 'property_id', "id");
    }
}
