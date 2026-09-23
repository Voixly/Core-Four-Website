<?php

namespace App\Support;

class Assets
{
    public static function url(string $path): string
    {
        $relative = ltrim($path, '/');
        $full = public_path($relative);
        $version = is_file($full) ? filemtime($full) : null;

        return '/'.$relative.($version ? '?v='.$version : '');
    }
}
