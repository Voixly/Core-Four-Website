<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Renders page structures extracted from the previous site.
 * Colours are snapped onto the brand tokens so the palette stays in one place.
 */
class PageLayout
{
    /** Fractional widths for the source column classes. */
    public const WIDTHS = [
        'one' => 1.0,
        'one-second' => 0.5,
        'one-third' => 1 / 3,
        'two-third' => 2 / 3,
        'one-fourth' => 0.25,
        'three-fourth' => 0.75,
        'one-fifth' => 0.2,
        'two-fifth' => 0.4,
        'three-fifth' => 0.6,
        'four-fifth' => 0.8,
        'one-sixth' => 1 / 6,
        'five-sixth' => 5 / 6,
    ];

    /** Live colour values (oklch or rgb) mapped to brand tokens. */
    protected const COLOR_MAP = [
        'oklch(0.7055 0.1785 140.66)' => 'var(--lime-green)',
        'oklch(0.6099 0.1588 148.77)' => 'var(--bright-green)',
        'oklch(0.2496 0.031 155.1)' => 'var(--forest-green)',
        'oklch(0.4669 0.1097 150.1)' => 'var(--muted-green)',
        'oklch(1 0 none)' => 'var(--white)',
        'oklch(0.9761 0.0027 264.54)' => 'var(--white-ghost)',
        'rgb(255, 255, 255)' => 'var(--white)',
        'rgba(255, 255, 255, 0.45)' => 'rgba(255,255,255,.45)',
    ];

    public static function color(?string $value): ?string
    {
        if (! $value || $value === 'rgba(0, 0, 0, 0)' || $value === 'transparent') {
            return null;
        }

        return self::COLOR_MAP[$value] ?? $value;
    }

    /** Grid span out of 12 for a width class. */
    public static function span(string $width): int
    {
        $fraction = self::WIDTHS[$width] ?? 1.0;

        return max(1, (int) round($fraction * 12));
    }

    /** Read tablet/laptop/mobile width tokens off the source class lists. */
    public static function widthFromClasses(array $classes, string $breakpoint): ?string
    {
        $prefix = $breakpoint.'-';
        foreach ($classes as $class) {
            if (str_starts_with($class, $prefix)) {
                $token = substr($class, strlen($prefix));
                if (isset(self::WIDTHS[$token])) {
                    return $token;
                }
            }
        }

        return null;
    }

    /**
     * Source grids (comparison tables) override the declared column class, so the
     * measured span wins. The breakpoint classes then inherit that same override
     * instead of snapping back to full width — except on mobile, which stacks.
     */
    public static function spanVars(array $node): string
    {
        $classes = $node['classes'] ?? [];
        $declared = self::span($node['width'] ?? 'one');
        $span = $node['span'] ?? $declared;
        $vars = ['--span:'.$span];
        $overridden = $span !== $declared;

        foreach (['laptop', 'tablet', 'mobile'] as $breakpoint) {
            $width = self::widthFromClasses($classes, $breakpoint);

            if ($width === null) {
                continue;
            }

            $value = self::span($width);

            if ($overridden && $value === $declared && $breakpoint !== 'mobile') {
                $value = $span;
            }

            $vars[] = '--span-'.$breakpoint.':'.$value;
        }

        return implode(';', $vars);
    }

    public static function iconForTitle(string $title): string
    {
        $title = strtolower(trim($title));

        return match (true) {
            str_contains($title, 'afford') => 'fas fa-wallet',
            str_contains($title, 'efficien') => 'fas fa-sync-alt',
            str_contains($title, 'integr') => 'fas fa-handshake',
            str_contains($title, 'quality') => 'fas fa-award',
            str_contains($title, 'hub') => 'fas fa-map-pin',
            str_contains($title, 'storm') => 'fas fa-clock',
            str_contains($title, 'office') || str_contains($title, 'phone') => 'fas fa-phone',
            default => 'fas fa-check',
        };
    }

    /**
     * Turn leftover theme icon names (icon-linkedin, etc.) into Font Awesome
     * classes so the green circles never render empty.
     */
    public static function iconClass(?string $icon): string
    {
        $icon = trim((string) $icon);

        if ($icon === '' || str_starts_with($icon, '/') || str_contains($icon, ' fa-')) {
            return $icon;
        }

        $name = preg_replace('/^icon-/', '', $icon) ?? $icon;

        return match ($name) {
            'linkedin' => 'fab fa-linkedin-in',
            'facebook' => 'fab fa-facebook-f',
            'instagram' => 'fab fa-instagram',
            default => $icon,
        };
    }

    /** Profile URL + label for a social icon, matching the footer. */
    public static function socialForIcon(?string $icon): ?array
    {
        $key = strtolower((string) $icon);

        return match (true) {
            str_contains($key, 'facebook') => [
                'href' => 'https://www.facebook.com/corefourroofing/',
                'label' => 'Facebook',
            ],
            str_contains($key, 'instagram') => [
                'href' => 'https://www.instagram.com/corefourroofing/',
                'label' => 'Instagram',
            ],
            str_contains($key, 'linkedin') => [
                'href' => 'https://www.linkedin.com/company/core-four-roofing/',
                'label' => 'LinkedIn',
            ],
            default => null,
        };
    }

    /** Put the live Font Awesome icons back into stripped extractor HTML. */
    public static function restoreIcons(string $html): string
    {
        $queue = ['fas fa-map-pin', 'fas fa-clock', 'fas fa-phone'];
        $i = 0;

        return preg_replace_callback('/<i(?:\s+class="")?><\/i>/', function () use (&$queue, &$i) {
            $icon = $queue[$i] ?? 'fas fa-check';
            $i++;

            return '<i class="'.$icon.'" aria-hidden="true"></i>';
        }, $html) ?? $html;
    }

    /**
     * Heading and lede for the map card. Home uses the short coverage line;
     * storm / financing pages carry a longer rapid-response intro.
     */
    public static function coverageCopy(array $section): array
    {
        $html = self::firstMatchingHtml($section, static fn (string $h) => str_contains($h, 'Service Hubs'));
        $title = 'Protecting Texas,<br>One Roof at a Time';
        $lede = 'Core Four Roofing Service Coverage';

        if ($html && preg_match('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/si', $html, $heading)) {
            $title = trim($heading[1]);
        }

        if ($html && preg_match('/<\/h[1-6]>\s*<p>(.*?)<\/p>/si', $html, $intro)) {
            $lede = trim($intro[1]);
        }

        return ['title' => $title, 'lede' => $lede];
    }

    /** First HTML blob in a section tree that matches the predicate. */
    protected static function firstMatchingHtml(array $node, callable $match): ?string
    {
        if (isset($node['html']) && is_string($node['html']) && $match($node['html'])) {
            return $node['html'];
        }

        foreach ($node as $value) {
            if (is_array($value) && ($found = self::firstMatchingHtml($value, $match))) {
                return $found;
            }
        }

        return null;
    }

    public static function defaultFaq(): array
    {
        return [
            [
                'q' => 'Roof Consultation',
                'a' => 'We provide a comprehensive roof inspection to evaluate damage, assess lifespan, and determine the most cost-effective solution for your commercial or residential property.',
            ],
            [
                'q' => 'Professional Installation',
                'a' => 'From TPO and EPDM for commercial buildings to premium asphalt shingles for homes, our licensed crews install your new roof efficiently—often in just 1-3 days.',
            ],
            [
                'q' => 'Quality Control',
                'a' => 'After installation, we perform a rigorous quality inspection. We leave your property spotless and ensure you have all warranty documentation for complete peace of mind.',
            ],
        ];
    }

    public static function fraction(string $width): float
    {
        return self::WIDTHS[$width] ?? 1.0;
    }

    /**
     * Commercial/residential heroes store the before shot on the section and
     * the after shot on the first wrap. Those two photos must share one
     * full-bleed frame; painting the after on the content box crops it
     * differently and the before shows around the edges.
     */
    public static function heroPair(array $section): ?array
    {
        $before = $section['bgImage'] ?? null;
        $after = $section['wraps'][0]['box']['bgImage'] ?? null;

        if (! $before || ! $after || $before === $after) {
            return null;
        }

        return ['before' => $before, 'after' => $after];
    }

    /** Whether a section renders anything, as opposed to being a bare spacer. */
    public static function sectionHasContent(array $section): bool
    {
        $walk = function (array $node) use (&$walk): bool {
            $item = $node['item'] ?? [];

            if (! empty($item['type']) && (! empty($item['html']) || ! empty($item['text']) || ! empty($item['images']) || ! empty($item['img']))) {
                return true;
            }

            foreach (array_merge($node['wraps'] ?? [], $node['columns'] ?? []) as $child) {
                if ($walk($child)) {
                    return true;
                }
            }

            return false;
        };

        foreach ($section['wraps'] ?? [] as $wrap) {
            if ($walk($wrap)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The blog listing arrives as one flat run of h3/p/"Read More" per post with
     * no wrapper, so rebuild the per-post boxes the live three-up grid needs.
     */
    public static function blogPosts(string $html): string
    {
        $chunks = preg_split('/(?=<h3>)/', $html);
        $chunks = array_values(array_filter($chunks, fn ($c) => str_starts_with(trim($c), '<h3>')));

        if (count($chunks) < 3) {
            return '';
        }

        return implode('', array_map(fn ($c) => '<article class="blk-post">'.$c.'</article>', $chunks));
    }

    /**
     * Carry the colour/weight/leading measured off the live element. Font size is
     * left to the responsive scale so it still steps down on small screens.
     */
    public static function typeStyle(array $item): string
    {
        $parts = [];

        if ($color = self::color($item['color'] ?? null)) {
            $parts[] = 'color:'.$color;
        }

        if (! empty($item['weight'])) {
            $parts[] = 'font-weight:'.$item['weight'];
        }

        if (! empty($item['lh']) && ! empty($item['size'])) {
            $ratio = (float) str_replace('px', '', (string) $item['lh']) / (float) $item['size'];

            if ($ratio > 0) {
                $parts[] = 'line-height:'.round($ratio, 3);
            }
        }

        if (! empty($item['align']) && ! in_array($item['align'], ['start', 'left'], true)) {
            $parts[] = 'text-align:'.$item['align'];
        }

        if (! empty($item['transform']) && $item['transform'] !== 'none') {
            $parts[] = 'text-transform:'.$item['transform'];
        }

        if (strtolower((string) ($item['family'] ?? '')) === 'industry') {
            $parts[] = 'font-family:var(--header-font)';
        }

        return implode(';', $parts);
    }

    /**
     * Galleries flow into masonry columns. The column count is the
     * container width over the narrowest image, which is how the live grid lands
     * on three columns for photo walls and two for the hero badge block.
     */
    /** Small associate-member seals, not full-bleed photos. */
    public static function isLogoImage(array $item): bool
    {
        if (($item['type'] ?? '') !== 'image') {
            return false;
        }

        $src = (string) ($item['img']['src'] ?? '');

        if (str_contains($src, 'Associate-Members') || str_contains($src, 'home-advisor')) {
            return true;
        }

        $display = (int) ($item['img']['dw'] ?? $item['box']['w'] ?? 0);
        $intrinsic = (int) ($item['img']['w'] ?? 0);

        if ($display > 0 && $display <= 120) {
            return true;
        }

        return $display > 0 && $intrinsic >= $display * 2.5;
    }

    /** Dark or brand-green fills need light copy; white cards do not. */
    public static function needsLightText(array $box): bool
    {
        $bg = self::color($box['bg'] ?? null);
        if (! $bg) {
            return false;
        }

        $pale = [
            'var(--white)',
            'var(--white-ghost)',
            'var(--paper)',
            'rgb(255, 255, 255)',
            '#fff',
            '#ffffff',
        ];

        return ! in_array(strtolower($bg), $pale, true);
    }

    /** Coloured chip ("The Guarantee") — not a full-width bar. */
    public static function isPill(array $node): bool
    {
        if (($node['kind'] ?? 'column') === 'wrap') {
            return false;
        }

        $box = $node['box'] ?? [];
        $radius = (int) ($box['radius'] ?? 0);
        $height = (int) ($box['h'] ?? 0);

        return ! empty($box['bg']) && $radius >= 40 && $height > 0 && $height <= 48;
    }

    /** A wrap that is just a row of those seals. */
    public static function isLogoRow(array $node): bool
    {
        $cols = $node['columns'] ?? [];

        if (count($cols) < 2) {
            return false;
        }

        $logos = 0;
        foreach ($cols as $col) {
            if (self::isLogoImage($col['item'] ?? [])) {
                $logos++;
            }
        }

        return $logos >= 2 && $logos === count($cols);
    }

    /** Small seals in a single row (residential / commercial), not a 2x2 card. */
    public static function isLogoStrip(array $node): bool
    {
        if (! self::isLogoRow($node)) {
            return false;
        }

        $cols = $node['columns'] ?? [];
        if (count($cols) >= 5) {
            return true;
        }

        foreach ($cols as $col) {
            $span = (int) ($col['span'] ?? 12);
            $width = (int) ($col['item']['img']['dw'] ?? $col['item']['box']['w'] ?? $col['box']['w'] ?? 0);
            if ($span > 3 && $width > 120) {
                return false;
            }
        }

        return true;
    }

    public static function galleryColumns(array $item): int
    {
        $width = $item['box']['w'] ?? null;
        $widths = array_filter(array_column($item['images'] ?? [], 'w'));

        if (! $width || ! $widths) {
            return 1;
        }

        return max(1, min(4, (int) round($width / min($widths))));
    }

    /**
     * Comparison tables declare every cell full-width and rely on a grid
     * the extractor didn't capture. When the measured boxes show the children
     * actually sit on one row, rebuild that row from their real widths.
     */
    public static function rowColumns(array $node): string
    {
        $kids = array_merge($node['columns'] ?? [], $node['wraps'] ?? []);
        $width = $node['box']['w'] ?? null;

        if (count($kids) < 2 || ! $width) {
            return '';
        }

        $boxes = array_map(fn ($k) => $k['box'] ?? [], $kids);

        foreach ($boxes as $box) {
            if (empty($box['w'])) {
                return '';
            }
        }

        $ws = array_column($boxes, 'w');

        // Side by side iff the children's widths add up to roughly one container
        // width; stacked children would each be the full width and overshoot badly.
        $total = array_sum($ws);

        if ($total < $width * 0.8 || $total > $width * 1.15) {
            return '';
        }

        $declared = 0;
        foreach ($kids as $kid) {
            $declared += $kid['span'] ?? self::span($kid['width'] ?? 'one');
        }

        if ($declared === 12) {
            return '';
        }

        return implode(' ', array_map(fn ($w) => $w.'fr', $ws));
    }

    /**
     * Source card images sit flush against the bottom of their card. Full-width
     * images round only the bottom corners; inset ones round only the top.
     */
    public static function mediaClass(array $node, ?array $parent): string
    {
        if (($node['item']['type'] ?? null) !== 'image' || ! $parent) {
            return '';
        }

        $card = $parent['box'] ?? [];
        $box = $node['box'] ?? [];
        $isCard = ! empty($card['bg']) || ! empty($card['bgImage']) || ! empty($card['radius']);

        if (! $isCard || ! isset($card['y'], $card['h'], $card['w'], $box['y'], $box['h'], $box['w'])) {
            return '';
        }

        if (abs(($box['y'] + $box['h']) - ($card['y'] + $card['h'])) > 4) {
            return '';
        }

        return $box['w'] >= $card['w'] - 4 ? ' blk-media-bleed' : ' blk-media-inset';
    }

    /** Build an inline style string from an extracted box. */
    public static function boxStyle(?array $box, bool $withPadding = true): string
    {
        if (! $box) {
            return '';
        }

        $rules = [];

        if ($bg = self::color($box['bg'] ?? null)) {
            $rules[] = "background-color:$bg";
        }

        $photo = ! empty($box['bgImage']) && ! str_contains((string) $box['bgImage'], '.svg');

        if (! empty($box['bgImage'])) {
            $rules[] = "background-image:url('".e($box['bgImage'])."')";
            $rules[] = 'background-size:cover';
            $rules[] = 'background-position:center';
        }

        if (! empty($box['radius'])) {
            $rules[] = 'border-radius:'.$box['radius'].'px';
        }

        if ($withPadding && ! empty($box['padding']) && array_sum($box['padding']) > 0) {
            $pad = $box['padding'];
            // Extracted cards often pad 48/30/0/30, so the last line or button
            // sits in the rounded corner and overflow:hidden clips it. Photo
            // cards and cards whose last child is an image keep a 0 floor —
            // CSS pulls those pictures flush to the bottom edge.
            $filled = $photo || ! empty($box['bg']);
            $pill = ($box['radius'] ?? 0) >= 40 && ($box['h'] ?? 0) > 0 && ($box['h'] ?? 0) <= 48;
            // Only cards extracted with a 0 floor (48/30/0/30) need a bottom
            // inset so the last line clears the radius. Even padding, such as
            // the frosted hero cards at 16px, should stay as measured.
            if ($filled && ! $photo && ! $pill && ($box['radius'] ?? 0) && (int) ($pad[2] ?? 0) === 0) {
                $pad[2] = 48;
            }
            $rules[] = 'padding:'.implode('px ', $pad).'px';
        }

        if ($photo && ! empty($box['h']) && (int) $box['h'] >= 280) {
            $rules[] = 'min-height:'.(int) $box['h'].'px';
        }

        return implode(';', $rules);
    }

    /** Rewrite absolute live URLs onto local routes. */
    public static function href(?string $url): string
    {
        if (! $url) {
            return '#';
        }

        if (Str::startsWith($url, ['tel:', 'mailto:', '#'])) {
            return $url;
        }

        $url = preg_replace('#^https?://(www\.)?corefourroofing\.com#', '', $url) ?: '/';

        if (preg_match('#^/contact-core-four-roofing/contact/?$#', $url)) {
            return '/contact-core-four-roofing/';
        }

        return $url === '' ? '/' : $url;
    }

    /** Is this an internal anchor to the page's own contact form? */
    public static function isAnchor(string $url): bool
    {
        return Str::contains($url, '#') && ! Str::startsWith($url, ['tel:', 'mailto:']);
    }

    /**
     * Decide which visual treatment a button gets, based on its live background.
     */
    public static function buttonClass(?array $item): string
    {
        $bg = $item['bg'] ?? null;

        return match ($bg) {
            'oklch(0.7055 0.1785 140.66)' => 'btn',
            'oklch(1 0 none)', 'rgb(255, 255, 255)' => 'btn btn--white',
            null, 'rgba(0, 0, 0, 0)' => 'btn btn--ghost',
            default => 'btn btn--grey',
        };
    }

    /** Keep captured WordPress links on this site when the old domain comes down. */
    protected static function localizeUrls(mixed $value): mixed
    {
        if (is_string($value)) {
            return str_replace(
                ['https://www.corefourroofing.com/', 'http://www.corefourroofing.com/', 'https://corefourroofing.com/', 'http://corefourroofing.com/'],
                '/',
                $value
            );
        }

        if (is_array($value)) {
            return array_map([self::class, 'localizeUrls'], $value);
        }

        return $value;
    }

    /** Load an extracted page definition. */
    public static function page(string $slug): ?array
    {
        $path = resource_path('data/pages/'.$slug.'.json');

        if (! is_file($path)) {
            return null;
        }

        $page = json_decode(file_get_contents($path), true);
        if (! is_array($page)) {
            return null;
        }

        $page = self::localizeUrls($page);

        $seo = SiteSeo::forPage($slug);
        $page['title'] = $seo['title'];
        $page['description'] = $seo['description'];

        return $page;
    }
}
