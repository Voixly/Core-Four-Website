<?php

namespace App\Support;

class BlogPost
{
    /**
     * @return list<array{slug: string, title: string, description: string, date: string, image: ?string, html: string}>
     */
    public static function all(): array
    {
        $posts = [];
        foreach (static::slugs() as $slug) {
            $post = static::find($slug);
            if ($post) {
                $posts[] = $post;
            }
        }

        return $posts;
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        $indexed = [];
        $path = resource_path('data/posts/index.json');
        if (is_file($path)) {
            $decoded = json_decode((string) file_get_contents($path), true);
            if (is_array($decoded)) {
                $indexed = array_values(array_filter($decoded, 'is_string'));
            }
        }

        $onDisk = [];
        foreach (glob(resource_path('data/posts/*.json')) ?: [] as $file) {
            $slug = basename($file, '.json');
            if ($slug === 'index' || ! preg_match('/^[a-z0-9-]+$/', $slug)) {
                continue;
            }
            $onDisk[] = $slug;
        }

        $extra = array_values(array_diff($onDisk, $indexed));
        sort($extra);

        return array_values(array_unique([...$indexed, ...$extra]));
    }

    /**
     * @return array{slug: string, title: string, description: string, date: string, image: ?string, html: string}|null
     */
    public static function find(string $slug): ?array
    {
        if (! preg_match('/^[a-z0-9-]+$/', $slug)) {
            return null;
        }

        $path = resource_path('data/posts/'.$slug.'.json');
        if (! is_file($path)) {
            return null;
        }

        $post = json_decode((string) file_get_contents($path), true);
        if (! is_array($post) || empty($post['slug']) || empty($post['title'])) {
            return null;
        }

        return $post;
    }
}
