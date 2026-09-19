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

        return implode(';', $parts);
    }

    /**
     * Galleries flow into masonry columns. The column count is the
     * container width over the narrowest image, which is how the live grid lands
     * on three columns for photo walls and two for the hero badge block.
     */
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

        if (! empty($box['bgImage'])) {
            $rules[] = "background-image:url('".e($box['bgImage'])."')";
            $rules[] = 'background-size:cover';
            $rules[] = 'background-position:center';
        }

        if (! empty($box['radius'])) {
            $rules[] = 'border-radius:'.$box['radius'].'px';
        }

        if ($withPadding && ! empty($box['padding']) && array_sum($box['padding']) > 0) {
            $rules[] = 'padding:'.implode('px ', $box['padding']).'px';
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

        $url = preg_replace('#^https?://(www\.)?corefourroofing\.com#', '', $url);

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

    /** Load an extracted page definition. */
    public static function page(string $slug): ?array
    {
        $path = resource_path('data/pages/'.$slug.'.json');

        if (! is_file($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }
}
