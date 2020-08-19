<?php

namespace App;

use App\Traits\Imageable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Game extends Model
{
    use HasSlug, Imageable;

    //
    protected $fillable = [
        'name', 'image', 'description'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function characters()
    {
        return $this->hasMany(Character::class);
    }
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
