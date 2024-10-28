<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Style Tags
        $styles = [
            ['name' => 'Photorealistic', 'color' => '#2563EB'],
            ['name' => 'Anime', 'color' => '#DB2777'],
            ['name' => 'Digital Art', 'color' => '#7C3AED'],
            ['name' => 'Oil Painting', 'color' => '#EA580C'],
            ['name' => 'Watercolor', 'color' => '#0891B2'],
            ['name' => 'Sketch', 'color' => '#4B5563'],
        ];

        foreach ($styles as $style) {
            Tag::create([
                'name' => $style['name'],
                'slug' => Str::slug($style['name']),
                'type' => 'style',
                'color' => $style['color'],
                'description' => "Images in {$style['name']} style",
            ]);
        }

        // Mood Tags
        $moods = [
            ['name' => 'Dreamy', 'color' => '#8B5CF6'],
            ['name' => 'Dark', 'color' => '#1F2937'],
            ['name' => 'Vibrant', 'color' => '#EF4444'],
            ['name' => 'Peaceful', 'color' => '#10B981'],
            ['name' => 'Mysterious', 'color' => '#6366F1'],
            ['name' => 'Energetic', 'color' => '#F59E0B'],
        ];

        foreach ($moods as $mood) {
            Tag::create([
                'name' => $mood['name'],
                'slug' => Str::slug($mood['name']),
                'type' => 'mood',
                'color' => $mood['color'],
                'description' => "Images with a {$mood['name']} mood",
            ]);
        }

        // Subject Tags
        $subjects = [
            ['name' => 'Portrait', 'color' => '#EC4899'],
            ['name' => 'Landscape', 'color' => '#059669'],
            ['name' => 'Architecture', 'color' => '#9333EA'],
            ['name' => 'Abstract', 'color' => '#3B82F6'],
            ['name' => 'Character', 'color' => '#F97316'],
            ['name' => 'Nature', 'color' => '#22C55E'],
        ];

        foreach ($subjects as $subject) {
            Tag::create([
                'name' => $subject['name'],
                'slug' => Str::slug($subject['name']),
                'type' => 'subject',
                'color' => $subject['color'],
                'description' => "{$subject['name']} focused images",
            ]);
        }
    }
}
