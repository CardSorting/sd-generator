<?php

namespace App\Utils;

class PromptUtils
{
    public static function validatePrompt(string $prompt): bool
    {
        $minLength = 3;
        $maxLength = 1000;
        
        return strlen(trim($prompt)) >= $minLength && 
               strlen(trim($prompt)) <= $maxLength;
    }

    public static function sanitizePrompt(string $prompt): string
    {
        // Remove potentially harmful characters
        $prompt = strip_tags($prompt);
        
        // Normalize whitespace
        $prompt = preg_replace('/\s+/', ' ', trim($prompt));
        
        return $prompt;
    }

    public static function extractTags(string $prompt): array
    {
        // Extract words that start with #
        preg_match_all('/#(\w+)/', $prompt, $matches);
        
        return $matches[1] ?? [];
    }

    public static function suggestTags(string $prompt): array
    {
        // Common art style keywords
        $artStyles = [
            'digital art',
            'oil painting',
            'watercolor',
            'sketch',
            'anime',
            'photorealistic',
        ];

        $suggestions = [];
        foreach ($artStyles as $style) {
            if (stripos($prompt, $style) !== false) {
                $suggestions[] = $style;
            }
        }

        return $suggestions;
    }

    public static function analyzePromptComplexity(string $prompt): array
    {
        return [
            'length' => strlen($prompt),
            'word_count' => str_word_count($prompt),
            'has_negative' => stripos($prompt, 'no ') !== false || 
                            stripos($prompt, 'without ') !== false,
            'has_colors' => (bool) preg_match('/\b(red|blue|green|yellow|purple|black|white)\b/i', $prompt),
            'has_numbers' => (bool) preg_match('/\d+/', $prompt),
        ];
    }

    public static function getPromptSuggestions(): array
    {
        return [
            'portrait' => [
                'A detailed portrait of a person with {style} style',
                'Close-up portrait emphasizing {feature} in {style}',
            ],
            'landscape' => [
                'A beautiful landscape showing {scene} in {style} style',
                'Panoramic view of {location} during {time}',
            ],
            'concept' => [
                'Abstract representation of {concept} using {style}',
                'Surreal interpretation of {theme} with {element}',
            ],
        ];
    }
}
