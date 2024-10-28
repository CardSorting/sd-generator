<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_public',
        'user_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $with = ['imageGenerations'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the user who owns the collection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the image generations in this collection.
     */
    public function imageGenerations(): BelongsToMany
    {
        return $this->belongsToMany(ImageGeneration::class, 'collection_image_generation')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include public collections.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
