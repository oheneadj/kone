<?php

namespace App\Contracts;

interface VideoServiceInterface
{
    /**
     * Check if the provided URL is a valid YouTube video URL.
     *
     * This method validates whether the given URL string corresponds to a legitimate YouTube video link.
     * It checks for standard YouTube URL patterns and ensures the video ID exists.
     *
     * @param string $url The URL to validate (e.g., 'https://www.youtube.com/watch?v=VIDEO_ID' or 'https://youtu.be/VIDEO_ID')
     * @return string|null Returns the video ID if valid, null if invalid
     * @throws \InvalidArgumentException If the URL is malformed or empty
     * @example
     *   $service->isValidVideoUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ'); // Returns 'dQw4w9WgXcQ'
     *   $service->isValidVideoUrl('https://youtu.be/dQw4w9WgXcQ'); // Returns 'dQw4w9WgXcQ'
     *   $service->isValidVideoUrl('https://invalid-url.com'); // Returns null
     */


    public function isAvalidVideoUrl(string $url): bool;

    public function extractVideoId(string $url): ?string;
}
