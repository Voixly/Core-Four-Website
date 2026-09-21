<?php

namespace App\Support;

use App\Models\City;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class CityPage
{
    /** @var array<string, array<string, mixed>>|null */
    protected static ?array $profiles = null;

    /**
     * @return array<string, mixed>
     */
    public static function make(City $city): array
    {
        $profile = static::profile($city->slug);
        $isRes = $city->type === 'residential';
        $isHub = $city->slug === 'tx';
        $name = $city->name;
        $state = $city->state ?: 'TX';

        $title = $isHub
            ? ($isRes
                ? 'Residential Roofing Across Texas | Core Four Roofing'
                : 'Commercial Roofing Across Texas | Core Four Roofing')
            : ($isRes
                ? static::residentialTitle($name, $profile)
                : 'Commercial Roofing in '.$name.', TX | Core Four Roofing');

        $description = static::description($city, $profile, $isRes, $isHub);
        $h1 = $isHub
            ? ($isRes ? 'Residential roofing across Texas' : 'Commercial roofing across Texas')
            : ($isRes
                ? 'Roof repair and replacement in '.$name.', Texas'
                : 'Commercial roofing in '.$name.', Texas');

        $nearby = static::nearbyCities($city, $profile);
        $sibling = City::query()
            ->where('slug', $city->slug)
            ->where('type', $isRes ? 'commercial' : 'residential')
            ->first();

        return [
            'title' => $title,
            'description' => $description,
            'h1' => $h1,
            'kicker' => $isHub
                ? 'Texas coverage · Tomball HQ'
                : ($profile['county'].' · '.$city->metro.' metro'),
            'lead' => static::lead($city, $profile, $isRes, $isHub),
            'about' => static::about($city, $profile, $isRes, $isHub),
            'weather' => static::weather($city, $profile, $isRes),
            'services_heading' => $isRes
                ? 'What we install and repair in '.$name
                : 'Commercial roofing services in '.$name,
            'services' => static::services($city, $profile, $isRes),
            'faqs' => static::faqs($city, $profile, $isRes, $isHub),
            'zips' => $profile['zips'] ?? [],
            'neighborhoods' => $profile['neighborhoods'] ?? [],
            'county' => $profile['county'] ?? ($city->metro.' County'),
            'drive' => $profile['drive'] ?? '',
            'housing' => $profile['housing'] ?? '',
            'nearby' => $nearby,
            'sibling' => $sibling,
            'is_hub' => $isHub,
            'canonical' => SiteSeo::url($city->path()),
            'schema' => static::schema($city, $profile, $title, $description, $h1, $isRes, $isHub),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function profile(string $slug): array
    {
        $all = static::profiles();

        return $all[$slug] ?? [
            'county' => 'Texas',
            'zips' => [],
            'neighborhoods' => [],
            'nearby' => [],
            'drive' => 'Dispatched from Tomball HQ',
            'lat' => 30.0972,
            'lng' => -95.6161,
            'housing' => 'Local homes and buildings with the roof systems common to this Texas market.',
            'focus' => ['repair', 'storm'],
            'storm' => 'Texas hail, wind, and heat. We inspect for the weather this city actually sees.',
            'note' => 'Core Four Roofing serves this market from Tomball.',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function profiles(): array
    {
        if (static::$profiles !== null) {
            return static::$profiles;
        }

        $path = database_path('data/city-seo.json');
        static::$profiles = File::exists($path)
            ? (json_decode((string) File::get($path), true) ?: [])
            : [];

        return static::$profiles;
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    protected static function residentialTitle(string $name, array $profile): string
    {
        $focus = $profile['focus'] ?? [];
        $primary = $focus[0] ?? 'repair';

        return match ($primary) {
            'tile' => $name.', TX Tile Roof Repair & Replacement | Core Four',
            'slate' => $name.' Slate & Tile Roofing | Core Four Roofing',
            'metal' => $name.' Metal Roof Repair & Replacement | Core Four',
            'scs' => $name.' Stone-Coated Steel Roofing | Core Four Roofing',
            default => 'Roof Repair & Replacement in '.$name.', TX | Core Four',
        };
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    protected static function description(City $city, array $profile, bool $isRes, bool $isHub): string
    {
        $phone = config('app.office_phone');
        $hood = $profile['neighborhoods'][0] ?? $city->name;

        if ($isHub) {
            return $isRes
                ? 'Core Four Roofing repairs and replaces homes across Houston, Austin, and DFW from Tomball. Call '.$phone.'.'
                : 'Commercial roof surveys, TPO, metal, and maintenance across Texas. Tomball-based crews. Call '.$phone.'.';
        }

        if ($isRes) {
            return 'Roofing company in '.$city->name.', TX — tile, metal, stone-coated steel, and storm repair in '.$hood.' and '.$profile['county'].'. Call '.$phone.'.';
        }

        return 'Commercial roofing in '.$city->name.', TX. Surveys, TPO and metal, night and weekend work around tenants. '.$profile['drive'].'. Call '.$phone.'.';
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    protected static function lead(City $city, array $profile, bool $isRes, bool $isHub): string
    {
        if ($isHub) {
            return $isRes
                ? 'We cover Houston suburbs every week, plus scheduled work in Austin and Dallas–Fort Worth.'
                : 'Commercial roofs in Houston, Austin, and DFW are scheduled from Tomball.';
        }

        if ($isRes) {
            return ($profile['note'] ?? '').' '.$profile['drive'].'.';
        }

        return 'Property in '.$city->name.' cannot sit with a slow leak. We survey, photograph, and schedule around tenants — '.$profile['drive'].'.';
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return list<string>
     */
    protected static function about(City $city, array $profile, bool $isRes, bool $isHub): array
    {
        $name = $city->name;
        $hoods = static::list($profile['neighborhoods'] ?? []);
        $zips = static::list($profile['zips'] ?? []);

        if ($isHub) {
            return [
                'Core Four Roofing is based at 22955 State Highway 249 Suite 26, Tomball, TX 77375. Houston-suburb work is the daily route. Austin and Dallas–Fort Worth jobs are scheduled Texas coverage with a local spec for that metro.',
                'Katy tile, Galveston metal, and Plano slate are different jobs. We write the spec for the roof that is actually on the building.',
            ];
        }

        if ($isRes) {
            return [
                $profile['note'],
                $name.' housing: '.$profile['housing'],
                ($hoods ? 'Neighborhoods we already inspect: '.$hoods.'. ' : '').($zips ? 'ZIP codes we work: '.$zips.'. ' : '').'County: '.$profile['county'].'.',
            ];
        }

        return [
            'Commercial roofing in '.$name.' is a survey-first job. We photograph the field, flashings, and drainage, then bid TPO, metal, coating, or repair without shutting the building down if we can help it.',
            $profile['note'],
            ($zips ? 'We work properties in '.$zips.'. ' : '').'From Tomball HQ: '.$profile['drive'].'.',
        ];
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    protected static function weather(City $city, array $profile, bool $isRes): string
    {
        $who = $isRes ? 'homes' : 'buildings';

        return ($profile['storm'] ?? 'Texas weather is hard on '.$who.' in '.$city->name.'.').' We inspect for the failure this market actually sees.';
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return list<array{title: string, body: string, href: string}>
     */
    protected static function services(City $city, array $profile, bool $isRes): array
    {
        $name = $city->name;
        $focus = $profile['focus'] ?? [];

        if (! $isRes) {
            return [
                ['title' => 'Roof replacement & installation', 'body' => 'TPO, metal, and built-up systems on '.$name.' commercial buildings. We schedule around tenants and dock hours.', 'href' => '/commercial-roofing/roof-replacement-installation/'],
                ['title' => 'Repair & preventative maintenance', 'body' => 'Leak response and a maintenance plan so the same flashing does not fail twice.', 'href' => '/commercial-roofing/repair-preventative-maintenance/'],
                ['title' => 'Coatings & restoration', 'body' => 'When the deck is sound, a coating can buy years without a full tear-off.', 'href' => '/commercial-roofing/coatings-restoration/'],
                ['title' => 'Inspections & condition reports', 'body' => 'A written report property managers can send to ownership — photos, not a shrug.', 'href' => '/commercial-roofing/inspections-condition-reports/'],
            ];
        }

        $items = [
            ['title' => 'Roof repair', 'body' => 'Leaks, flashing, and storm damage on '.$name.' homes — including tile, metal, and shingle.', 'href' => '/residential-roofing/roof-repair/'],
            ['title' => 'Metal roofs', 'body' => 'Standing-seam and residential metal repair and replacement. Hidden fasteners, not barn panels.', 'href' => '/residential-roofing/metal-roofs/'],
            ['title' => 'Stone-coated steel', 'body' => 'The upgrade when tile is too heavy or the underlayment is gone — tile look, metal strength.', 'href' => '/residential-roofing/stone-coated-steel/'],
            ['title' => 'Insurance claims', 'body' => 'We document '.$name.' storm damage so the claim matches the roof, not a drive-by estimate.', 'href' => '/insurance-claims/'],
        ];

        if (in_array('tile', $focus, true) || in_array('slate', $focus, true)) {
            array_unshift($items, [
                'title' => 'Tile & slate repair',
                'body' => $name.' has real tile'.(in_array('slate', $focus, true) ? ' and slate' : '').'. We repair broken pieces and failed underlayment — we do not walk a tile roof like a shingle roof.',
                'href' => '/residential-roofing/roof-repair/',
            ]);
        }

        return array_slice($items, 0, 4);
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return list<array{q: string, a: string}>
     */
    protected static function faqs(City $city, array $profile, bool $isRes, bool $isHub): array
    {
        $name = $city->name;
        $hood = $profile['neighborhoods'][0] ?? $name;
        $phone = config('app.office_phone');

        if ($isHub) {
            return [
                ['q' => 'Where is Core Four Roofing based?', 'a' => '22955 State Highway 249 Suite 26, Tomball, TX 77375. Houston-suburb crews run daily. Austin and DFW are scheduled Texas coverage.'],
                ['q' => 'Do you work outside the Houston suburbs?', 'a' => 'Yes. Houston-suburb crews run daily. Austin and Dallas–Fort Worth jobs are scheduled Texas coverage with a spec for that metro.'],
                ['q' => 'Do you take residential and commercial work statewide?', 'a' => 'Yes — with a local spec. A Galveston metal roof and a Frisco tile roof are not the same job.'],
            ];
        }

        if ($isRes) {
            return [
                [
                    'q' => 'Do you repair roofs in '.$hood.' and the rest of '.$name.'?',
                    'a' => 'Yes. We inspect in '.(static::list($profile['neighborhoods'] ?? []) ?: $name).'. If the house is in '.$profile['county'].', start with a photo inspection and we will tell you if it is a repair or a replace.',
                ],
                [
                    'q' => 'How far is '.$name.' from your Tomball shop?',
                    'a' => $profile['drive'].'. Emergency tarping is 24/7. Production days are scheduled so the house is dried-in before the next front.',
                ],
                [
                    'q' => 'What roof types do you work on in '.$name.'?',
                    'a' => $profile['housing'].' Specialty work is tile, stone-coated steel, metal, and slate.',
                ],
                [
                    'q' => 'Can you help with a hail or insurance claim in '.$name.'?',
                    'a' => 'Yes. We photograph slopes, openings, and interior stains so the '.$profile['county'].' claim matches the roof. Call '.$phone.'.',
                ],
                [
                    'q' => 'Do I need to be home for the inspection?',
                    'a' => 'Not always. We can inspect from the exterior and send photos the same day. If we need attic access we will schedule it.',
                ],
            ];
        }

        return [
            [
                'q' => 'Do you work on occupied commercial buildings in '.$name.'?',
                'a' => 'Yes. We survey and bid around tenants, docks, and office hours. Night and weekend production is normal when the building cannot close.',
            ],
            [
                'q' => 'How quickly can you get to a '.$name.' commercial leak?',
                'a' => $profile['drive'].'. We tarp and stabilize first, then write the repair or replace so the interior stops taking water.',
            ],
            [
                'q' => 'What systems do you install on '.$name.' commercial roofs?',
                'a' => 'TPO, metal, coatings, and repair of existing built-up or modified systems. The survey decides — not a catalog default.',
            ],
            [
                'q' => 'Can property managers get a written condition report?',
                'a' => 'Yes. Photos, recommendations, and a budget range you can send to ownership. Start at '.$phone.'.',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return Collection<int, City>
     */
    protected static function nearbyCities(City $city, array $profile): Collection
    {
        $slugs = $profile['nearby'] ?? [];
        $found = collect();

        if ($slugs) {
            $found = City::query()
                ->where('type', $city->type)
                ->whereIn('slug', $slugs)
                ->where('slug', '!=', $city->slug)
                ->orderBy('name')
                ->get();
        }

        if ($found->count() >= 4 || $city->slug === 'tx') {
            return $found;
        }

        $extra = City::query()
            ->where('type', $city->type)
            ->where('metro', $city->metro)
            ->where('slug', '!=', $city->slug)
            ->where('slug', '!=', 'tx')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return $found->concat($extra)->unique('id')->take(8)->values();
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return array<int, array<string, mixed>>
     */
    protected static function schema(City $city, array $profile, string $title, string $description, string $h1, bool $isRes, bool $isHub): array
    {
        $url = SiteSeo::url($city->path());
        $home = SiteSeo::url('/');
        $phone = config('app.office_phone');
        $faqs = static::faqs($city, $profile, $isRes, $isHub);

        $areaServed = $isHub
            ? [
                ['@type' => 'State', 'name' => 'Texas'],
                ['@type' => 'City', 'name' => 'Houston'],
                ['@type' => 'City', 'name' => 'Austin'],
                ['@type' => 'City', 'name' => 'Dallas'],
            ]
            : [[
                '@type' => 'City',
                'name' => $city->name,
                'containedInPlace' => [
                    '@type' => 'State',
                    'name' => 'Texas',
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $profile['lat'] ?? 30.0972,
                    'longitude' => $profile['lng'] ?? -95.6161,
                ],
            ]];

        $graph = [
            [
                '@type' => 'RoofingContractor',
                '@id' => $home.'#business',
                'name' => 'Core Four Roofing',
                'url' => $home,
                'telephone' => $phone,
                'image' => SiteSeo::shareImage(),
                'logo' => SiteSeo::url('/images/android-chrome-512x512.png'),
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
                'areaServed' => $areaServed,
                'sameAs' => [
                    'https://www.facebook.com/corefourroofing/',
                    'https://www.instagram.com/corefourroofing/',
                    'https://www.linkedin.com/company/core-four-roofing/',
                    'https://www.yelp.com/biz/core-four-roofing-tomball-3',
                ],
            ],
            [
                '@type' => 'WebPage',
                '@id' => $url.'#webpage',
                'url' => $url,
                'name' => $title,
                'headline' => $h1,
                'description' => $description,
                'inLanguage' => 'en-US',
                'isPartOf' => ['@type' => 'WebSite', 'name' => 'Core Four Roofing', 'url' => $home],
                'about' => [
                    '@type' => 'Service',
                    'name' => ($isRes ? 'Residential' : 'Commercial').' roofing in '.$city->name,
                    'provider' => ['@id' => $home.'#business'],
                    'areaServed' => $areaServed,
                    'serviceType' => $isRes
                        ? ['Roof repair', 'Roof replacement', 'Tile roofing', 'Metal roofing', 'Stone-coated steel']
                        : ['Commercial roofing', 'TPO', 'Metal roofing', 'Roof coating', 'Roof inspection'],
                ],
                'speakable' => [
                    '@type' => 'SpeakableSpecification',
                    'cssSelector' => ['.city-lead', '.city-about', '.city-faq'],
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Service areas', 'item' => SiteSeo::url('/service-areas/')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $city->name.' roofing', 'item' => $url],
                ],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn (array $faq) => [
                    '@type' => 'Question',
                    'name' => $faq['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
                ], $faqs),
            ],
        ];

        return $graph;
    }

    /**
     * @param  list<string>  $items
     */
    protected static function list(array $items): string
    {
        $items = array_values(array_filter($items));
        if ($items === []) {
            return '';
        }
        if (count($items) === 1) {
            return $items[0];
        }

        $last = array_pop($items);

        return implode(', ', $items).' and '.$last;
    }
}
