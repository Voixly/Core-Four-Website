<?php

namespace App\Support;

use App\Models\City;
use Illuminate\Support\Collection;

class PerformanceReport
{
    public string $title = 'Performance report 2026';

    public string $window = 'Oct 19 – Nov 14, 2026';

    public string $gbpWindow = 'Oct 19 – Nov 13, 2026';

    public string $gaWindow = 'Oct 19 – Nov 15, 2026';

    public string $ytd = 'Search and social through Nov 14, 2026';

    public string $prepared = 'Nov 16, 2026';

    public string $filename = 'core-four-performance-report-2026.pdf';

    /**
     * @return array<string, mixed>
     */
    public static function make(): array
    {
        $report = new self;

        return [
            'meta' => [
                'title' => $report->title,
                'window' => $report->window,
                'gbp_window' => $report->gbpWindow,
                'ga_window' => $report->gaWindow,
                'ytd' => $report->ytd,
                'prepared' => $report->prepared,
                'filename' => $report->filename,
                'location' => '22955 TX-249 Ste 26, Tomball, TX',
                'growth' => '~12%',
            ],
            'queries' => $report->queries(),
            'pages' => $report->pages(),
            'posts' => $report->posts(),
            'landings' => $report->landings(),
            'listings' => $report->listings(),
            'press' => $report->press(),
            'charts' => $report->charts(),
        ];
    }

    /**
     * @return list<array{query: string, clicks: int, impressions: int, position: float}>
     */
    public function queries(): array
    {
        return [
            ['query' => 'core four roofing', 'clicks' => 34, 'change' => '+42%'],
            ['query' => 'tile roofing sugar land', 'clicks' => 3, 'change' => 'New'],
            ['query' => 'core roofing systems', 'clicks' => 1, 'change' => 'New'],
            ['query' => 'roofing companies in houston tx', 'clicks' => 1, 'change' => 'New'],
            ['query' => 'roofing contractors in austin', 'clicks' => 1, 'change' => 'New'],
            ['query' => 'paid training jobs near me', 'clicks' => 1, 'change' => 'New'],
            ['query' => 'project coordinator jobs near me', 'clicks' => 1, 'change' => 'New'],
            ['query' => 'where can i get a personal loan of up to $15000 to repair my roof in texas?', 'clicks' => 1, 'change' => 'New'],
        ];
    }

    /**
     * @return list<array{page: string, clicks: int, impressions: int}>
     */
    public function pages(): array
    {
        return [
            ['page' => '/', 'clicks' => 46, 'change' => '+21%'],
            ['page' => 'www.corefourroofing.com/', 'clicks' => 5, 'change' => '+25%'],
            ['page' => 'http://www.corefourroofing.com/', 'clicks' => 3, 'change' => 'New'],
            ['page' => '/residential-roofing-in-sugar-land-tx/', 'clicks' => 3, 'change' => 'New'],
            ['page' => '/financing/', 'clicks' => 2, 'change' => 'New'],
            ['page' => '/service-areas/', 'clicks' => 2, 'change' => '+100%'],
            ['page' => '/residential-roofing-in-conroe-tx/', 'clicks' => 1, 'change' => 'New'],
        ];
    }

    /**
     * Articles that name Core Four, checked on the publications’ sites Oct 19, 2026.
     *
     * @return list<array{date: string, publication: string, title: string, url: string}>
     */
    public function press(): array
    {
        return [
            [
                'date' => 'Oct 19, 2026',
                'publication' => 'Texas Gazette',
                'title' => 'Commercial Roofing in Houston: TPO, Metal or a Roof Coating?',
                'url' => 'https://txgazette.com/articles/commercial-roofing-in-houston-tpo-metal-or-a-roof-coating',
            ],
            [
                'date' => 'Oct 19, 2026',
                'publication' => 'Texas Gazette',
                'title' => 'Roof Replacement in Tomball, TX: How to Tell Your Roof Is Finished',
                'url' => 'https://txgazette.com/articles/roof-replacement-in-tomball-tx-how-to-tell-your-roof-is-finished',
            ],
            [
                'date' => 'Oct 13, 2026',
                'publication' => 'Dock Line Magazine',
                'title' => 'Four Questions Before Anyone Touches the Roof on a North Houston House',
                'url' => 'https://docklinemagazine.com/business/four-questions-residential-roof-tomball-woodlands-conroe',
            ],
            [
                'date' => 'Oct 10, 2026',
                'publication' => 'Dock Line Magazine',
                'title' => 'Repair or replace: shingles, metal, and stone-coated steel on Houston-area houses',
                'url' => 'https://docklinemagazine.com/business/repair-or-replace-houston-roof-shingles-metal-steel',
            ],
            [
                'date' => 'Oct 9, 2026',
                'publication' => 'Dock Line Magazine',
                'title' => 'When a Houston commercial roof is leaking, coat it or replace it',
                'url' => 'https://docklinemagazine.com/business/houston-commercial-roof-leak-coat-or-replace',
            ],
            [
                'date' => 'Oct 7, 2026',
                'publication' => 'Dock Line Magazine',
                'title' => 'What to do in the first two days after hail hits a north Houston roof',
                'url' => 'https://docklinemagazine.com/business/hail-damage-roof-tomball-woodlands-conroe',
            ],
            [
                'date' => 'Oct 2, 2026',
                'publication' => 'Dock Line Magazine',
                'title' => 'After Thursday night’s Houston storms, how to check a roof for leaks',
                'url' => 'https://docklinemagazine.com/community/houston-storm-roof-check-katy-cypress-woodlands',
            ],
        ];
    }

    /**
     * @return list<array{posted: string, theme: string, views: int, engagement: int}>
     */
    public function posts(): array
    {
        return [
            ['posted' => 'Oct 30', 'theme' => 'A commercial roof as a long-term property asset', 'views' => 347, 'engagement' => 8],
            ['posted' => 'Oct 25', 'theme' => 'How a commercial repair meets the existing roof', 'views' => 112, 'engagement' => 1],
            ['posted' => 'Oct 21', 'theme' => 'What sits under a residential roof', 'views' => 104, 'engagement' => 3],
            ['posted' => 'Oct 28', 'theme' => 'Checking a roof after severe weather', 'views' => 79, 'engagement' => 2],
            ['posted' => 'Oct 23', 'theme' => 'Roofing an outdoor structure', 'views' => 78, 'engagement' => 4],
        ];
    }

    /**
     * @return array{residential: array{count: int, metros: list<array{label: string, cities: list<array{city: string, url: string}>}>}, commercial: array{count: int, metros: list<array{label: string, cities: list<array{city: string, url: string}>}>}}
     */
    public function landings(): array
    {
        $labels = [
            'Houston' => 'Houston Metro',
            'Austin' => 'Austin Metro',
            'Dallas' => 'Dallas–Fort Worth',
            'Texas' => 'Texas',
        ];
        $order = ['Houston', 'Austin', 'Dallas', 'Texas'];

        $group = function (string $type, ?array $onlyMetros) use ($labels, $order): array {
            $cities = $this->cityRows($type);
            if ($onlyMetros !== null) {
                $cities = $cities->whereIn('metro', $onlyMetros)->values();
            }

            $metros = [];
            foreach ($order as $metro) {
                $items = $cities->where('metro', $metro)->values();
                if ($items->isEmpty()) {
                    continue;
                }
                $metros[] = [
                    'label' => $labels[$metro] ?? $metro,
                    'cities' => $items->map(fn (array $row) => [
                        'city' => $row['name'],
                        'url' => $row['url'],
                    ])->all(),
                ];
            }

            return [
                'count' => $cities->count(),
                'metros' => $metros,
            ];
        };

        return [
            'residential' => $group('residential', ['Houston', 'Texas']),
            'commercial' => $group('commercial', null),
        ];
    }

    /**
     * @return array{featured: list<array{name: string, url: string}>, all: list<array{name: string, url: string}>, count: int, linked: int}
     */
    public function listings(): array
    {
        $all = $this->directoryRows();
        $featuredNames = [
            'Google Business Profile', 'Apple', 'Bing', 'Yelp', 'Nextdoor',
            'Better Business Bureau', 'MapQuest', 'Yahoo!', 'Waze', 'Siri',
            'Amazon Alexa', 'TikTok', 'Gemini', 'OpenAI',
        ];

        $byName = [];
        foreach ($all as $row) {
            $byName[$row['name']] = $row;
        }

        $featured = [];
        foreach ($featuredNames as $name) {
            if (isset($byName[$name])) {
                $featured[] = $byName[$name];
            }
        }

        return [
            'featured' => $featured,
            'all' => $all,
            'count' => count($all),
            'linked' => count(array_filter($all, fn (array $row) => $row['url'] !== '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function charts(): array
    {
        return [
            'gsc' => [
                ['week' => 'Oct 19', 'clicks' => 8, 'impressions' => 455, 'position' => 23.4, 'ctr' => 1.76],
                ['week' => 'Oct 20', 'clicks' => 3, 'impressions' => 242, 'position' => 40.3, 'ctr' => 1.24],
                ['week' => 'Oct 21', 'clicks' => 3, 'impressions' => 282, 'position' => 42.1, 'ctr' => 1.06],
                ['week' => 'Oct 22', 'clicks' => 1, 'impressions' => 345, 'position' => 33.7, 'ctr' => 0.29],
                ['week' => 'Oct 23', 'clicks' => 4, 'impressions' => 275, 'position' => 42.9, 'ctr' => 1.45],
                ['week' => 'Oct 24', 'clicks' => 2, 'impressions' => 309, 'position' => 40.5, 'ctr' => 0.65],
                ['week' => 'Oct 25', 'clicks' => 1, 'impressions' => 378, 'position' => 39.2, 'ctr' => 0.26],
                ['week' => 'Oct 26', 'clicks' => 1, 'impressions' => 360, 'position' => 34.7, 'ctr' => 0.28],
                ['week' => 'Oct 27', 'clicks' => 5, 'impressions' => 363, 'position' => 39.2, 'ctr' => 1.38],
                ['week' => 'Oct 28', 'clicks' => 2, 'impressions' => 342, 'position' => 39.2, 'ctr' => 0.58],
                ['week' => 'Oct 29', 'clicks' => 6, 'impressions' => 499, 'position' => 32.1, 'ctr' => 1.20],
                ['week' => 'Oct 30', 'clicks' => 2, 'impressions' => 502, 'position' => 37.4, 'ctr' => 0.40],
                ['week' => 'Oct 31', 'clicks' => 2, 'impressions' => 336, 'position' => 43.1, 'ctr' => 0.60],
                ['week' => 'Nov 1', 'clicks' => 2, 'impressions' => 476, 'position' => 39.0, 'ctr' => 0.42],
                ['week' => 'Nov 2', 'clicks' => 3, 'impressions' => 455, 'position' => 36.0, 'ctr' => 0.66],
                ['week' => 'Nov 3', 'clicks' => 2, 'impressions' => 340, 'position' => 38.9, 'ctr' => 0.59],
                ['week' => 'Nov 4', 'clicks' => 6, 'impressions' => 579, 'position' => 33.0, 'ctr' => 1.04],
                ['week' => 'Nov 5', 'clicks' => 1, 'impressions' => 350, 'position' => 36.6, 'ctr' => 0.29],
                ['week' => 'Nov 6', 'clicks' => 0, 'impressions' => 196, 'position' => 18.3, 'ctr' => 0],
                ['week' => 'Nov 7', 'clicks' => 0, 'impressions' => 196, 'position' => 19.5, 'ctr' => 0],
                ['week' => 'Nov 8', 'clicks' => 0, 'impressions' => 292, 'position' => 23.8, 'ctr' => 0],
                ['week' => 'Nov 9', 'clicks' => 2, 'impressions' => 234, 'position' => 15.1, 'ctr' => 0.85],
                ['week' => 'Nov 10', 'clicks' => 3, 'impressions' => 236, 'position' => 23.9, 'ctr' => 1.27],
                ['week' => 'Nov 11', 'clicks' => 0, 'impressions' => 289, 'position' => 24.9, 'ctr' => 0],
                ['week' => 'Nov 12', 'clicks' => 0, 'impressions' => 228, 'position' => 25.9, 'ctr' => 0],
                ['week' => 'Nov 13', 'clicks' => 3, 'impressions' => 258, 'position' => 35.9, 'ctr' => 1.16],
                ['week' => 'Nov 14', 'clicks' => 0, 'impressions' => 280, 'position' => 28.9, 'ctr' => 0],
            ],
            'ga' => [
                'labels' => ['Oct 19', 'Oct 20', 'Oct 21', 'Oct 22', 'Oct 23', 'Oct 24', 'Oct 25', 'Oct 26', 'Oct 27', 'Oct 28', 'Oct 29', 'Oct 30', 'Oct 31', 'Nov 1', 'Nov 2', 'Nov 3', 'Nov 4', 'Nov 5', 'Nov 6', 'Nov 7', 'Nov 8', 'Nov 9', 'Nov 10', 'Nov 11', 'Nov 12', 'Nov 13', 'Nov 14', 'Nov 15'],
                'current' => [22, 6316, 24, 16, 12641, 22, 14, 6649, 6851, 35, 12229, 26, 28, 7480, 32, 26, 6184, 39, 24, 7218, 9, 24, 7090, 22, 5715, 34, 6184, 11],
                'previous' => [],
            ],
            'gbp' => [
                'actions' => [
                    'labels' => ['Directions', 'Website', 'Calls', 'Messages'],
                    'data' => [51, 26, 5, 0],
                ],
                'views' => [
                    'labels' => ['Maps desktop 82', 'Search mobile 53', 'Maps mobile 38', 'Search desktop 34'],
                    'data' => [82, 53, 38, 34],
                ],
            ],
            'sources' => [
                'labels' => ['t.co 89,455', 'Direct 724', 'Not set 95', 'Google organic 88', 'Bing 16', 'Facebook 6'],
                'data' => [89455, 724, 95, 88, 16, 6],
            ],
            'countries' => [
                'labels' => ['United States 54', 'Pakistan 3', 'United Kingdom 2', 'Bangladesh 1', 'India 1', 'Mexico 1'],
                'data' => [54, 3, 2, 1, 1, 1],
            ],
            'facebook' => [
                ['d' => 'Oct 19', 'v' => 70], ['d' => 'Oct 21', 'v' => 104], ['d' => 'Oct 23', 'v' => 78],
                ['d' => 'Oct 25', 'v' => 112], ['d' => 'Oct 28', 'v' => 79], ['d' => 'Oct 30', 'v' => 347],
                ['d' => 'Nov 4', 'v' => 75], ['d' => 'Nov 6', 'v' => 63], ['d' => 'Nov 7', 'v' => 66],
                ['d' => 'Nov 9', 'v' => 70], ['d' => 'Nov 11', 'v' => 31], ['d' => 'Nov 14', 'v' => 27],
            ],
            'instagram' => [
                'labels' => ['Oct 19', 'Oct 21', 'Oct 23', 'Oct 25', 'Oct 28', 'Oct 30', 'Nov 4', 'Nov 6', 'Nov 7', 'Nov 9', 'Nov 11', 'Nov 14'],
                'data' => [35, 24, 31, 32, 12, 199, 10, 18, 19, 17, 125, 7],
            ],
        ];
    }

    /**
     * @return Collection<int, array{name: string, metro: string, url: string}>
     */
    protected function cityRows(string $type): Collection
    {
        try {
            if (City::query()->where('type', $type)->exists()) {
                return City::query()
                    ->where('type', $type)
                    ->orderBy('name')
                    ->get()
                    ->map(fn (City $city) => [
                        'name' => $city->name,
                        'metro' => $city->metro,
                        'url' => url($city->path()),
                    ]);
            }
        } catch (\Throwable) {
            // Fall through to the landing-page JSON if the table is not ready.
        }

        return $this->citiesFromJson($type);
    }

    /**
     * @return Collection<int, array{name: string, metro: string, url: string}>
     */
    protected function citiesFromJson(string $type): Collection
    {
        $path = database_path('data/landing-pages.json');
        if (! is_file($path)) {
            $path = dirname(base_path()).'/report/data/landing-pages.json';
        }
        if (! is_file($path)) {
            return collect();
        }

        $data = json_decode((string) file_get_contents($path), true) ?: [];
        $houston = [
            'Houston', 'Pasadena', 'The Woodlands', 'Sugar Land', 'Conroe', 'Katy', 'Pearland',
            'League City', 'Missouri City', 'Baytown', 'Cypress', 'Spring', 'Tomball', 'Humble',
            'Richmond', 'Rosenberg', 'Friendswood', 'Alvin', 'Dickinson', 'Fulshear', 'Atascocita', 'Galveston',
        ];
        $austin = ['Austin', 'Round Rock', 'Georgetown', 'Cedar Park', 'Leander', 'Pflugerville', 'Manor', 'Kyle', 'Buda', 'Hutto', 'Lakeway'];

        return collect($data[$type] ?? [])->map(function (array $row) use ($houston, $austin) {
            $metro = 'Dallas';
            if (in_array($row['city'], $houston, true)) {
                $metro = 'Houston';
            } elseif (in_array($row['city'], $austin, true)) {
                $metro = 'Austin';
            } elseif (str_contains((string) $row['city'], 'statewide')) {
                $metro = 'Texas';
            }

            return [
                'name' => $row['city'],
                'metro' => $metro,
                'url' => $row['url'],
            ];
        })->values();
    }

    /**
     * @return list<array{name: string, url: string}>
     */
    protected function directoryRows(): array
    {
        $path = database_path('data/listings.json');
        if (! is_file($path)) {
            $path = dirname(base_path()).'/report/data/listings.json';
        }
        if (! is_file($path)) {
            return [];
        }

        $rows = json_decode((string) file_get_contents($path), true) ?: [];

        return array_map(function (array $row) {
            $url = trim((string) ($row['url'] ?? ''));
            if ($url !== '' && str_contains($url, 'yelp.com')) {
                $url = strtok($url, '?') ?: $url;
            }

            return [
                'name' => (string) $row['name'],
                'url' => $url,
            ];
        }, $rows);
    }
}
