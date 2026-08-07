<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('image_url')) {
    /**
     * Resolve a stored image path to a usable URL.
     * Falls back to a placeholder when the path is empty.
     */
    function image_url(?string $path, string $placeholderText = 'Odik+Shop'): string
    {
        if (empty($path)) {
            return 'https://placehold.co/600x600/e6f2f0/008060?text='.urlencode($placeholderText);
        }

        // Absolute URLs pass through untouched.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('is_video')) {
    /**
     * Determine whether the given path points to a video file.
     */
    function is_video(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['mp4', 'webm', 'ogg', 'mov'], true);
    }
}

if (! function_exists('bdt')) {
    /**
     * Format a number as a Bangladeshi Taka amount (e.g. "BDT 1,299").
     */
    function bdt($amount): string
    {
        return 'BDT '.number_format((float) $amount);
    }
}
