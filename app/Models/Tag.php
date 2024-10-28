<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type', // 'style', 'mood', 'subject', etc.
        'description',
        'color',
    ];

    public function imageGenerations(): BelongsToMany
    {
        return $this->belongsToMany(ImageGeneration::class);
    }

    public function models(): BelongsToMany
    {
        return $this->belongsToMany(SDModel::class, 'model_tags');
    }
}
