<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SDModel extends Model
{
    use HasFactory;

    protected $table = 'sd_models';

    protected $fillable = [
        'name',
        'category',
        'style_type',
        'description',
        'preview_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
