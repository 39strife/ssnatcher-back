<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use Notifiable;

    public static function boot()
    {
        parent::boot();


        self::created(function ($model) {
            $profile = new Profile();
            $model->profile()->save($profile);
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verification'
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }

    protected $with = ['profile'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRole()
    {
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function combos()
    {
        return $this->hasMany(Combo::class, "user_id", "id");
    }
    public function posts()
    {
        return $this->hasMany(Post::class, "user_id", "id");
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
