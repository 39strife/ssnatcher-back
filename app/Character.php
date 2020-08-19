<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Character extends Model
{
    use HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    //
    protected $fillable = ['name', 'image', 'description'];
    protected $hidden = ['game_id'];
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
