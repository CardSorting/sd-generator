<?php

namespace App\Utils;

class ModelUtils
{
    public static function categorizeModel(string $modelName): string
    {
        $modelName = strtolower($modelName);
        
        if (str_contains($modelName, ['anime', 'manga'])) {
            return 'anime';
        }
        
        if (str_contains($modelName, ['realistic', 'photo', 'real'])) {
            return 'realistic';
        }
        
        if (str_contains($modelName, ['paint', 'art'])) {
            return 'artistic';
        }
        
        return 'base';
    }

    public static function determineStyleType(string $modelName): string
    {
        $modelName = strtolower($modelName);
        
        if (str_contains($modelName, ['character', 'portrait'])) {
            return 'character';
        }
        
        if (str_contains($modelName, ['landscape', 'scene'])) {
            return 'landscape';
        }
        
        if (str_contains($modelName, ['concept', 'abstract'])) {
            return 'concept';
        }
        
        return 'general';
    }

    public static function getRecommendedSettings(string $modelName): array
    {
        $baseSettings = [
            'steps' => 20,
            'cfg_scale' => 7.0,
            'width' => 512,
            'height' => 512,
            'sampler_name' => 'Euler a',
        ];

        $category = self::categorizeModel($modelName);
        
        switch ($category) {
            case 'anime':
                return array_merge($baseSettings, [
                    'steps' => 28,
                    'cfg_scale' => 8.0,
                ]);
            
            case 'realistic':
                return array_merge($baseSettings, [
                    'steps' => 40,
                    'cfg_scale' => 7.5,
                ]);
            
            case 'artistic':
                return array_merge($baseSettings, [
                    'steps' => 30,
                    'cfg_scale' => 7.0,
                ]);
            
            default:
                return $baseSettings;
        }
    }

    public static function validateSettings(array $settings): array
    {
        $errors = [];
        
        if ($settings['steps'] < 1 || $settings['steps'] > 150) {
            $errors['steps'] = 'Steps must be between 1 and 150';
        }
        
        if ($settings['cfg_scale'] < 1 || $settings['cfg_scale'] > 30) {
            $errors['cfg_scale'] = 'CFG Scale must be between 1 and 30';
        }
        
        if (!ImageUtils::validateImageDimensions($settings['width'], $settings['height'])) {
            $errors['dimensions'] = 'Invalid image dimensions';
        }
        
        return $errors;
    }

    public static function getSupportedSamplers(): array
    {
        return [
            'Euler a' => [
                'name' => 'Euler a',
                'description' => 'Good all-around sampler',
                'recommended_steps' => 20,
            ],
            'DDIM' => [
                'name' => 'DDIM',
                'description' => 'Fast, good for iterative generation',
                'recommended_steps' => 20,
            ],
            'LMS' => [
                'name' => 'LMS',
                'description' => 'Good for detailed images',
                'recommended_steps' => 30,
            ],
        ];
    }
}
