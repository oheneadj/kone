<?php

namespace App\Services;

use App\Contracts\VideoServiceInterface;

class YouTubeService implements VideoServiceInterface
{
    public function isAvalidVideoUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $host = strtolower($host);

        return str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be');
    }

    public function extractVideoId(string $url): ?string
    {
        if (!$this->isAvalidVideoUrl($url)) {
            return null;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        $path = parse_url($url, PHP_URL_PATH);
        $host = strtolower(parse_url($url, PHP_URL_HOST));

        if (str_contains($host, 'youtube.com')) {
            parse_str($query, $params);
            return $params['v'] ?? null;
        }

        if (str_contains($host, 'youtu.be')) {
            return ltrim($path, '/');
        }

        return null;
    }
}
