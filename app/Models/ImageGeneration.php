<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ImageGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prompt',
        'negative_prompt',
        'settings',
        'status',
        'result',
        'error',
    ];

    protected $casts = [
        'settings' => 'array',
        'result' => 'array',
    ];

    /**
     * Get the user who created the image generation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the image generation.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the likes for the image generation.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the users who liked this image generation.
     */
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * Get the collections that include this image generation.
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)->withTimestamps();
    }

    /**
     * Get the tags for this image generation.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
