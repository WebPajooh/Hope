<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [
        'score',
    ];

    protected $hidden = [
        'password',
    ];

    protected $with = ['country'];

    public function isFollowedBy($userId): bool
    {
        return $this
            ->followers()
            ->where('follower_id', $userId)
            ->exists();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function achievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}
