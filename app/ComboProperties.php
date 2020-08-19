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
        return $this->belongsTo(Property::class, 'property_id', "id");
    }
    public function combo()
    {
        return $this->belongsTo(Combo::class, 'property_id', "id");
    }
}
