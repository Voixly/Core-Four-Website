<?php

namespace App\Support;

class SiteSeo
{
    public const DEFAULT_TITLE = 'Core Four Roofing | Commercial & Residential Roofing in Texas';

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
            'decoding-commercial-roof-inspections-protecting-your-texas-facility-and-investment' => 'Commercial Roof Inspections in Texas | Core Four',
            default => $title.' | Core Four Roofing',
        };
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

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => $url.'#article',
            'headline' => $post['title'],
            'description' => $post['description'],
            'datePublished' => $post['date'],
            'image' => $image,
            'mainEntityOfPage' => $url,
            'author' => [
                '@type' => 'Organization',
                'name' => 'Core Four Roofing',
                'url' => static::url('/'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Core Four Roofing',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => static::url('/images/android-chrome-512x512.png'),
                ],
            ],
        ];
    }
}
