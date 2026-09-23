<?php

namespace App\Support;

class SiteSeo
{
    public const DEFAULT_TITLE = 'Core Four Roofing | Commercial & Home Roofing in Texas';

    public const DEFAULT_DESCRIPTION = 'Texas commercial and residential roofing from Tomball. Repair, replacement, and 24/7 storm response across Houston, Dallas, and Austin.';

    public const SHARE_PATH = '/images/social-share.webp';

    public const SHARE_WIDTH = 1200;

    public const SHARE_HEIGHT = 676;

    public const SHARE_ALT = 'Core Four Roofing — a Texas home with the Houston skyline';

    public const SHARE_TYPE = 'image/webp';

    public const THEME_COLOR = '#269A47';

    public const FACEBOOK = 'https://www.facebook.com/corefourroofing/';

    public const INSTAGRAM = 'https://www.instagram.com/corefourroofing/';

    public const LINKEDIN = 'https://www.linkedin.com/company/core-four-roofing/';

    public const YELP = 'https://www.yelp.com/biz/core-four-roofing-tomball-3';

    /**
     * Public origin for canonicals, Open Graph, sitemap, and schema.
     * Local APP_URL hosts are never used — those leak into social cards.
     */
    public static function origin(): string
    {
        $configured = rtrim((string) config('app.public_url'), '/');
        $app = rtrim((string) config('app.url'), '/');
        $host = parse_url($app, PHP_URL_HOST) ?: '';

        if ($host === '' || in_array($host, ['127.0.0.1', 'localhost', '0.0.0.0'], true) || str_ends_with($host, '.local') || str_ends_with($host, '.hostingersite.com')) {
            return $configured !== '' ? $configured : 'https://corefourroofing.com';
        }

        return $app;
    }

    public static function url(?string $path = null): string
    {
        $origin = rtrim(static::origin(), '/');

        if ($path === null || $path === '' || $path === '/') {
            return $origin.'/';
        }

        return $origin.'/'.ltrim($path, '/');
    }

    public static function current(): string
    {
        $path = request()->getPathInfo() ?: '/';
        if ($path !== '/' && ! str_ends_with($path, '/')) {
            $path .= '/';
        }

        return static::url($path);
    }

    /**
     * Search titles stay near 60 characters. The article h1 keeps the full headline.
     */
    public static function articleDocumentTitle(string $slug, string $title): string
    {
        return match ($slug) {
            'navigating-code-for-commercial-roof-drainage-systems' => 'Commercial Roof Drainage Code in Texas | Core Four',
            'beat-the-ercot-heat-bifacial-solar-prep-vs-commercial-roof-energy-efficiency' => 'Commercial Roof Energy Efficiency in Texas | Core Four',
            'thermal-drone-mapping-texas-commercial-roof-hail-damage' => 'Thermal Drone Mapping for Texas Roof Hail | Core Four',
            'beyond-the-manufacturers-brochure-why-elite-roofing-certifications-matter-in-property-management' => 'Why Roofing Certifications Matter | Core Four Roofing',
            'stop-tearing-it-down-how-restoration-extends-your-asset-value-by-ten-plus-years' => 'Commercial Roof Coatings That Add Ten Years | Core Four',
            'preserving-hospitality-assets-how-structural-roof-maintenance-protects-the-guest-experience' => 'Commercial Roof Maintenance for Hotels | Core Four',
            'the-flat-roof-lifespan-battle-choosing-the-right-system-for-your-texas-facility' => 'Choosing a Flat Roof System in Texas | Core Four',
            'decoding-commercial-roof-inspections-protecting-your-texas-facility-and-investment' => 'What a Commercial Roof Inspection Shows | Core Four',
            'paying-for-a-roof-without-draining-savings' => 'Paying for a Roof Without Draining Savings | Core Four',
            default => $title.' | Core Four Roofing',
        };
    }

    /**
     * Visible page graph: the business, the site, this URL, and a breadcrumb.
     *
     * @return array<string, mixed>
     */
    public static function pageGraph(string $title, string $description): array
    {
        $graph = static::organizationSchema()['@graph'];
        $home = static::url('/');
        $url = static::current();
        $crumbs = static::breadcrumbs();

        $graph[] = [
            '@type' => 'WebPage',
            '@id' => $url.'#webpage',
            'url' => $url,
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'en-US',
            'isPartOf' => ['@id' => $home.'#website'],
            'about' => ['@id' => $home.'#business'],
            'breadcrumb' => count($crumbs) > 1 ? ['@id' => $url.'#breadcrumb'] : null,
        ];

        if (count($crumbs) > 1) {
            $graph[] = [
                '@type' => 'BreadcrumbList',
                '@id' => $url.'#breadcrumb',
                'itemListElement' => array_map(fn (array $crumb, int $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['item'],
                ], $crumbs, array_keys($crumbs)),
            ];
        }

        $graph = array_map(function (array $node) {
            return array_filter($node, fn ($value) => $value !== null);
        }, $graph);

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }

    /**
     * A useful alt when the captured page left the attribute blank.
     */
    public static function imageAlt(?string $src, ?string $alt = null, string $fallback = 'Core Four Roofing project'): string
    {
        $alt = trim((string) $alt);
        if ($alt !== '') {
            return $alt;
        }

        $file = strtolower(urldecode(basename((string) (parse_url((string) $src, PHP_URL_PATH) ?: $src))));
        $file = preg_replace('/\.[a-z0-9]+$/', '', $file) ?? $file;

        $rules = [
            'home-advisor' => 'HomeAdvisor Screened and Approved badge',
            'angi' => 'Angi Super Service Award 2024 badge',
            'bbb' => 'BBB accredited business badge',
            'houston-area-roofing' => 'Houston Area Roofing Contractors Association member badge',
            'rcat' => 'Roofing Contractors Association of Texas member badge',
            'certainteed' => 'CertainTeed Select ShingleMaster badge',
            'gaf' => 'GAF Certified contractor badge',
            'malarkey' => 'Malarkey Emerald Pro Contractor badge',
            'iko' => 'IKO Craftsman Premier Contractor badge',
            'partner-decra' => 'DECRA roofing partner badge',
            'decra-villa' => 'DECRA Villa stone-coated steel roof',
            'decra' => 'Stone-coated steel roof by Core Four Roofing',
            'roof_reapair' => 'Core Four roof repair and maintenance',
            'roof_replacement' => 'Core Four roof replacement',
            'asphalt' => 'Asphalt shingle roof by Core Four Roofing',
            'synthetic' => 'Synthetic shingle roof by Core Four Roofing',
            'stone-coated' => 'Stone-coated steel roof by Core Four Roofing',
            'metal' => 'Metal roof by Core Four Roofing',
            'tpo' => 'TPO commercial roof by Core Four Roofing',
            'emergency' => 'Emergency roof repair by Core Four Roofing',
            'tarp' => 'Storm tarp installed by Core Four Roofing',
            'insurance' => 'Roof documented for an insurance claim',
            'community' => 'Core Four Roofing in the community',
            'hero_before' => 'Roof before Core Four Roofing work',
            'hero_after' => 'Roof after Core Four Roofing work',
            'commercial' => 'Commercial roof by Core Four Roofing',
            'residential' => 'Residential roof by Core Four Roofing',
        ];

        foreach ($rules as $needle => $label) {
            if (str_contains($file, $needle)) {
                return $label;
            }
        }

        return $fallback;
    }

    public static function fillEmptyAlts(string $html): string
    {
        $filled = preg_replace_callback('/<img\b[^>]*>/i', function (array $match): string {
            $tag = $match[0];
            if (preg_match('/\balt=("|\')(.*?)\1/i', $tag, $existing) && trim(html_entity_decode($existing[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')) !== '') {
                return $tag;
            }
            if (! preg_match('/\bsrc=("|\')(.*?)\1/i', $tag, $src)) {
                return $tag;
            }

            $label = htmlspecialchars(static::imageAlt(html_entity_decode($src[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (preg_match('/\balt=("|\').*?\1/i', $tag)) {
                return preg_replace('/\balt=("|\').*?\1/i', 'alt="'.$label.'"', $tag, 1) ?? $tag;
            }

            return preg_replace('/<img\b/i', '<img alt="'.$label.'"', $tag, 1) ?? $tag;
        }, $html);

        return $filled ?? $html;
    }

    /**
     * @return list<array{name: string, item: string}>
     */
    public static function breadcrumbs(): array
    {
        $path = trim(request()->getPathInfo() ?: '/', '/');
        $crumbs = [['name' => 'Home', 'item' => static::url('/')]];
        if ($path === '') {
            return $crumbs;
        }

        $names = [
            'residential-roofing' => 'Residential roofing',
            'commercial-roofing' => 'Commercial roofing',
            'asphalt-shingles' => 'Asphalt shingles',
            'metal-roofs' => 'Metal roofs',
            'stone-coated-steel' => 'Stone-coated steel',
            'synthetic-roofs' => 'Synthetic roofs',
            'roof-repair' => 'Roof repair',
            'roof-installation' => 'Roof replacement',
            'roof-inspections' => 'Roof inspections',
            'roof-replacement-installation' => 'Roof replacement',
            'repair-preventative-maintenance' => 'Repair and maintenance',
            'coatings-restoration' => 'Coatings and restoration',
            'inspections-condition-reports' => 'Inspections',
            'insurance-claims' => 'Insurance claims',
            'storm-emergency' => 'Storm and emergency',
            'financing' => 'Financing',
            'about-core-four-roofing' => 'About',
            'service-areas' => 'Service areas',
            'contact-core-four-roofing' => 'Contact',
            'careers' => 'Careers',
            'blog' => 'Blog',
            'guides' => 'Guides',
            'privacy-policy' => 'Privacy policy',
            'terms' => 'Terms of use',
        ];

        $built = '';
        foreach (explode('/', $path) as $segment) {
            $built .= '/'.$segment;
            $crumbs[] = [
                'name' => $names[$segment] ?? ucwords(str_replace('-', ' ', $segment)),
                'item' => static::url($built.'/'),
            ];
        }

        return $crumbs;
    }

    public static function shareImage(): string
    {
        return static::url(static::SHARE_PATH);
    }

    /**
     * @return list<string>
     */
    public static function sameAs(): array
    {
        return [static::FACEBOOK, static::INSTAGRAM, static::LINKEDIN, static::YELP];
    }

    /**
     * Title and description overlays for captured WordPress pages.
     *
     * @return array{title: string, description: string}
     */
    public static function forPage(string $slug): array
    {
        return static::pages()[$slug] ?? [
            'title' => static::DEFAULT_TITLE,
            'description' => static::DEFAULT_DESCRIPTION,
        ];
    }

    /**
     * @return array<string, array{title: string, description: string}>
     */
    public static function pages(): array
    {
        return [
            'residential-roofing' => [
                'title' => 'Residential Roofing in Texas | Core Four Roofing',
                'description' => 'Roof repair and replacement for Texas homes. Shingles, metal, stone-coated steel, and synthetic across Houston, Dallas, and Austin.',
            ],
            'commercial-roofing' => [
                'title' => 'Commercial Roofing in Texas | Core Four Roofing',
                'description' => 'TPO, metal, coatings, and maintenance for Texas buildings. Replacement and inspections with crews that work around your hours.',
            ],
            'service-areas' => [
                'title' => 'Roofing Service Areas in Texas | Core Four Roofing',
                'description' => 'Core Four roofs homes and buildings from Tomball across Houston, Dallas, and Austin. See the cities we cover and request an inspection.',
            ],
            'about-core-four-roofing' => [
                'title' => 'About Core Four Roofing | Texas Roofing Crew',
                'description' => 'Locally owned Texas roofing company based in Tomball. Integrity, efficiency, quality, and affordability on homes and commercial buildings.',
            ],
            'contact-core-four-roofing' => [
                'title' => 'Contact Core Four Roofing | Free Roof Inspection',
                'description' => 'Request a free roof inspection. Tomball office, (281) 541-0027. Commercial bids and residential storm assessments within 24 hours.',
            ],
            'storm-emergency' => [
                'title' => '24/7 Emergency Roof Repair in Texas | Core Four',
                'description' => 'Storm damage? Same-day tarping, photo inspections, and insurance help for homes and buildings in Houston, Dallas, and Austin.',
            ],
            'financing' => [
                'title' => 'Roof Financing in Texas | Core Four Roofing',
                'description' => 'Flexible monthly payments for a new roof. Apply with Core Four’s lending partners without draining savings or operating capital.',
            ],
            'insurance-claims' => [
                'title' => 'Roof Insurance Claims in Texas | Core Four Roofing',
                'description' => 'Hail or wind damage? We document the roof, meet the adjuster, and fight for a full, code-compliant replacement — not a patch.',
            ],
            'blog' => [
                'title' => 'Roofing Advice from Core Four | Texas Roofing Blog',
                'description' => 'Notes from the Core Four crew on Texas weather, commercial drainage, and keeping a tight roof over a home or building.',
            ],
            'residential-roofing--asphalt-shingles' => [
                'title' => 'Asphalt Shingle Roofing in Texas | Core Four',
                'description' => 'Architectural asphalt shingles built for Texas hail and heat, installed by Core Four with manufacturer warranties.',
            ],
            'residential-roofing--metal-roofs' => [
                'title' => 'Metal Roofing in Texas | Core Four Roofing',
                'description' => 'Standing seam metal roofs for Texas homes — 50-plus year life, Class 4 hail rating, and a cooler attic than asphalt.',
            ],
            'residential-roofing--synthetic-roofs' => [
                'title' => 'F-Wave Synthetic Roofing in Texas | Core Four',
                'description' => 'Granule-free F-Wave synthetic shingles that look like slate or shake and stand up to Texas hail without the weight.',
            ],
            'residential-roofing--stone-coated-steel' => [
                'title' => 'Stone-Coated Steel Roofing in Texas | Core Four',
                'description' => 'Steel roofs with the look of tile, shake, or shingles — no cracking, splitting, or fading in Houston heat and hail.',
            ],
            'residential-roofing--roof-repair' => [
                'title' => 'Residential Roof Repair in Texas | Core Four',
                'description' => 'Leak detection and storm repairs for Texas homes. Stop water now and keep a small leak from becoming a tear-off.',
            ],
            'residential-roofing--roof-installation' => [
                'title' => 'Residential Roof Replacement in Texas | Core Four',
                'description' => 'Full tear-off and replacement for Texas homes. Premium materials, one-to-three day installs, lifetime workmanship.',
            ],
            'residential-roofing--roof-inspections' => [
                'title' => 'Residential Roof Inspections in Texas | Core Four',
                'description' => 'Photo-documented roof inspections for homeowners and buyers across Houston, Dallas, and Austin. Know the deck before you buy or replace.',
            ],
            'commercial-roofing--roof-replacement-installation' => [
                'title' => 'Commercial Roof Replacement in Texas | Core Four',
                'description' => 'Full-scale commercial replacements and new installs — TPO, metal, and low-slope — without shutting the building down.',
            ],
            'commercial-roofing--repair-preventative-maintenance' => [
                'title' => 'Commercial Roof Repair in Texas | Core Four',
                'description' => 'Rapid leak repair and preventative maintenance for flat and low-slope roofs in Houston, Dallas, and Austin.',
            ],
            'commercial-roofing--inspections-condition-reports' => [
                'title' => 'Commercial Roof Inspections in Texas | Core Four',
                'description' => 'Photo-documented condition reports for property managers, investors, and owners buying or selling a building.',
            ],
            'commercial-roofing--coatings-restoration' => [
                'title' => 'Commercial Roof Coatings in Texas | Core Four',
                'description' => 'Silicone and elastomeric coatings that waterproof a commercial roof and cut energy bills without a full tear-off.',
            ],
        ];
    }

    /**
     * Default JSON-LD for pages that do not supply their own graph.
     *
     * @return array<string, mixed>
     */
    public static function organizationSchema(): array
    {
        $home = static::url('/');

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'RoofingContractor',
                    '@id' => $home.'#business',
                    'name' => 'Core Four Roofing',
                    'url' => $home,
                    'telephone' => config('app.office_phone'),
                    'email' => 'hello@corefourroofing.com',
                    'image' => static::shareImage(),
                    'logo' => static::url('/images/android-chrome-512x512.png'),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => '22955 State Highway 249 Suite 26',
                        'addressLocality' => 'Tomball',
                        'addressRegion' => 'TX',
                        'postalCode' => '77375',
                        'addressCountry' => 'US',
                    ],
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => 30.0972,
                        'longitude' => -95.6161,
                    ],
                    'areaServed' => [
                        ['@type' => 'City', 'name' => 'Houston'],
                        ['@type' => 'City', 'name' => 'Austin'],
                        ['@type' => 'City', 'name' => 'Dallas'],
                        ['@type' => 'State', 'name' => 'Texas'],
                    ],
                    'sameAs' => static::sameAs(),
                    'openingHoursSpecification' => [
                        [
                            '@type' => 'OpeningHoursSpecification',
                            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                            'opens' => '07:00',
                            'closes' => '19:00',
                        ],
                    ],
                    'priceRange' => '$$',
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $home.'#website',
                    'url' => $home,
                    'name' => 'Core Four Roofing',
                    'publisher' => ['@id' => $home.'#business'],
                    'inLanguage' => 'en-US',
                ],
            ],
        ];
    }

    /**
     * @param  array{slug: string, title: string, description: string, date: string, image: ?string}  $post
     * @return array<string, mixed>
     */
    public static function articleSchema(array $post): array
    {
        $url = static::url('/'.$post['slug'].'/');
        $image = ! empty($post['image']) ? static::url($post['image']) : static::shareImage();

        $home = static::url('/');

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BlogPosting',
                    '@id' => $url.'#article',
                    'headline' => $post['title'],
                    'description' => $post['description'],
                    'datePublished' => $post['date'],
                    'dateModified' => $post['date'],
                    'image' => $image,
                    'mainEntityOfPage' => $url,
                    'author' => [
                        '@type' => 'Organization',
                        'name' => 'Core Four Roofing',
                        'url' => $home,
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => 'Core Four Roofing',
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => static::url('/images/android-chrome-512x512.png'),
                        ],
                    ],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    '@id' => $url.'#breadcrumb',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => static::url('/blog/')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => $url],
                    ],
                ],
            ],
        ];
    }
}
