<?php

namespace App;

use Actuallymab\LaravelComment\Contracts;
use App\Traits\Commentable;
use App\Traits\Imageable;
use App\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Combo extends Model
{
    use Rateable, Commentable, HasSlug, Imageable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'combo', 'image', 'description',
    ];
    protected $with = ['properties'];
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
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
