<?php

$root = dirname(__DIR__);
$postsJson = '/tmp/c4-wp-posts.json';
$outDir = $root.'/resources/data/posts';
$imgDir = $root.'/public/images/blog';
$listing = $root.'/tools/captured/blog-thumbs.json';
if (! is_file($listing)) {
    $listing = dirname($root).'/tools/captured/blog-thumbs.json';
}

@mkdir($outDir, 0755, true);
@mkdir($imgDir, 0755, true);

$posts = json_decode((string) file_get_contents($postsJson), true);
if (! is_array($posts)) {
    fwrite(STDERR, "Missing $postsJson\n");
    exit(1);
}

$thumbs = [];
if (is_file($listing)) {
    foreach (json_decode((string) file_get_contents($listing), true) ?: [] as $row) {
        $path = parse_url((string) ($row['href'] ?? ''), PHP_URL_PATH) ?: '';
        $slug = trim($path, '/');
        if ($slug && ! empty($row['local'])) {
            $thumbs[$slug] = $row['local'];
        }
    }
}

$downloaded = [];

$download = function (string $url) use ($imgDir, &$downloaded): string {
    $name = basename(parse_url($url, PHP_URL_PATH) ?: 'image.bin');
    $name = preg_replace('/[^A-Za-z0-9._-]/', '', $name) ?: 'image.bin';
    $dest = $imgDir.'/'.$name;
    $local = '/images/blog/'.$name;
    if (isset($downloaded[$url])) {
        return $downloaded[$url];
    }
    if (! is_file($dest)) {
        $data = @file_get_contents($url);
        if ($data === false || $data === '') {
            fwrite(STDERR, "skip $url\n");

            return $local;
        }
        file_put_contents($dest, $data);
        echo "img $name ".strlen($data)."\n";
    }
    $downloaded[$url] = $local;

    return $local;
};

$clean = function (string $html) use ($download): string {
    $html = preg_replace_callback(
        '#<div class="code-block[^"]*"[\s\S]*?<img([^>]+)>[\s\S]*?</div>\s*</div>\s*</div>#',
        fn ($m) => '<figure class="blog-figure"><img'.$m[1].'></figure>',
        $html
    ) ?? $html;

    $html = preg_replace('/\s(?:data-path-to-node|srcset|sizes|decoding)="[^"]*"/', '', $html) ?? $html;
    $html = preg_replace('/ class="[^"]*wp-image-[^"]*"/', '', $html) ?? $html;
    $html = preg_replace_callback(
        '#https?://(?:www\.)?corefourroofing\.com/wp-content/uploads/[^"\s]+#',
        fn ($m) => $download($m[0]),
        $html
    ) ?? $html;
    $html = str_replace('https://corefourroofing.com/', '/', $html);
    $html = str_replace('http://corefourroofing.com/', '/', $html);

    return trim($html);
};

$index = [];
foreach ($posts as $post) {
    $slug = (string) $post['slug'];
    $yoast = $post['yoast_head_json'] ?? [];
    $featUrl = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
    $image = $thumbs[$slug] ?? ($featUrl ? $download($featUrl) : null);

    $record = [
        'slug' => $slug,
        'title' => html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'description' => html_entity_decode((string) ($yoast['description'] ?? $yoast['og_description'] ?? strip_tags($post['excerpt']['rendered'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'date' => substr((string) $post['date'], 0, 10),
        'image' => $image,
        'html' => $clean((string) $post['content']['rendered']),
    ];
    file_put_contents($outDir.'/'.$slug.'.json', json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");
    $index[] = $slug;
    echo "post $slug\n";
}

file_put_contents($outDir.'/index.json', json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

$blogPage = $root.'/resources/data/pages/blog.json';
if (is_file($blogPage)) {
    $raw = file_get_contents($blogPage);
    $raw = str_replace('https://corefourroofing.com/', '/', $raw);
    $raw = str_replace('http://corefourroofing.com/', '/', $raw);
    file_put_contents($blogPage, $raw);
    echo "rewrote blog listing links\n";
}

echo 'done '.count($index)."\n";
