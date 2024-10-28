<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_generation_id',
    ];

    /**
     * Get the user who created the like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the image generation that was liked.
     */
    public function imageGeneration(): BelongsTo
    {
        return $this->belongsTo(ImageGeneration::class);
    }
}
