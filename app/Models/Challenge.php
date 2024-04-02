<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['user'];

    public function scopeByUserId(Builder $query, $userId = null): void
    {
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }
    }

    public function isLikedBy($userId): bool
    {
        return $this
            ->likes()
            ->where('user_id', $userId)
            ->exists();
    }

    public function passedDays(): int
    {
        return $this
            ->continued_at
            ->diff($this->created_at)
            ->d;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'continued_at' => 'datetime',
        ];
    }
}
