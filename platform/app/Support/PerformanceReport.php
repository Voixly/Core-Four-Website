<?php

namespace App\Support;

use App\Models\City;
use Illuminate\Support\Collection;

class PerformanceReport
{
    public string $title = 'Performance report 2026';

    public string $window = 'Aug 28 – Sep 21, 2026';

    public string $gbpWindow = 'Aug 30 – Sep 21, 2026';

    public string $ytd = 'Jan – Sep 21, 2026';

    public string $prepared = 'Sep 21, 2026';

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
            'charts' => $report->charts(),
        ];
    }

    /**
     * @return list<array{query: string, clicks: int, impressions: int, position: float}>
     */
    public function queries(): array
    {
        return [
            ['query' => 'core four roofing', 'clicks' => 27, 'impressions' => 96, 'position' => 1.07],
            ['query' => 'core roofing', 'clicks' => 3, 'impressions' => 25, 'position' => 4.79],
            ['query' => 'roofers close to me', 'clicks' => 2, 'impressions' => 6, 'position' => 3.00],
            ['query' => 'roof repair tomball', 'clicks' => 3, 'impressions' => 75, 'position' => 1.00],
            ['query' => 'commercial roofing', 'clicks' => 2, 'impressions' => 52, 'position' => 2.46],
            ['query' => 'commercial roof repair humble tx', 'clicks' => 2, 'impressions' => 99, 'position' => 10.22],
            ['query' => 'commercial roofing services humble tx', 'clicks' => 1, 'impressions' => 38, 'position' => 9.26],
            ['query' => 'roof estimate tomball texas', 'clicks' => 2, 'impressions' => 37, 'position' => 1.64],
            ['query' => 'residential roofing grapevine tx', 'clicks' => 1, 'impressions' => 63, 'position' => 1.00],
        ];
    }

    /**
     * @return list<array{page: string, clicks: int, impressions: int}>
     */
    public function pages(): array
    {
        return [
            ['page' => '/', 'clicks' => 47, 'impressions' => 3268],
            ['page' => '/commercial-roofing-in-league-city-tx/', 'clicks' => 4, 'impressions' => 54],
            ['page' => '/commercial-roofing-in-humble-tx/', 'clicks' => 3, 'impressions' => 168],
            ['page' => '/commercial-roofing-in-houston-tx/', 'clicks' => 3, 'impressions' => 69],
            ['page' => '/about-core-four-roofing/', 'clicks' => 2, 'impressions' => 158],
            ['page' => '/residential-roofing/metal-roofs/', 'clicks' => 2, 'impressions' => 128],
            ['page' => '/commercial-roofing-in-tomball-tx/', 'clicks' => 2, 'impressions' => 46],
            ['page' => '/service-areas/', 'clicks' => 1, 'impressions' => 190],
            ['page' => '/residential-roofing/', 'clicks' => 1, 'impressions' => 133],
        ];
    }

    /**
     * @return list<array{posted: string, theme: string, views: int, engagement: int}>
     */
    public function posts(): array
    {
        return [
            ['posted' => 'Sep 3', 'theme' => 'Botched tile repair — making it right', 'views' => 910, 'engagement' => 15],
            ['posted' => 'Aug 28', 'theme' => 'Culture on the jobsite', 'views' => 734, 'engagement' => 16],
            ['posted' => 'Aug 28', 'theme' => 'Irving commercial TPO replacement', 'views' => 542, 'engagement' => 19],
            ['posted' => 'Sep 1', 'theme' => 'Retail center — complex commercial', 'views' => 144, 'engagement' => 6],
            ['posted' => 'Sep 18', 'theme' => 'Houston · Dallas · Austin coverage', 'views' => 130, 'engagement' => 2],
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
                ['week' => 'Aug 28', 'clicks' => 3, 'impressions' => 242, 'position' => 36.0, 'ctr' => 1.24],
                ['week' => 'Aug 29', 'clicks' => 3, 'impressions' => 538, 'position' => 21.8, 'ctr' => 0.56],
                ['week' => 'Aug 30', 'clicks' => 2, 'impressions' => 553, 'position' => 19.4, 'ctr' => 0.36],
                ['week' => 'Aug 31', 'clicks' => 2, 'impressions' => 601, 'position' => 21.8, 'ctr' => 0.33],
                ['week' => 'Sep 1', 'clicks' => 6, 'impressions' => 392, 'position' => 20.1, 'ctr' => 1.53],
                ['week' => 'Sep 2', 'clicks' => 3, 'impressions' => 361, 'position' => 27.5, 'ctr' => 0.83],
                ['week' => 'Sep 3', 'clicks' => 2, 'impressions' => 256, 'position' => 21.3, 'ctr' => 0.78],
                ['week' => 'Sep 4', 'clicks' => 2, 'impressions' => 992, 'position' => 55.4, 'ctr' => 0.20],
                ['week' => 'Sep 5', 'clicks' => 2, 'impressions' => 270, 'position' => 35.4, 'ctr' => 0.74],
                ['week' => 'Sep 6', 'clicks' => 2, 'impressions' => 228, 'position' => 34.8, 'ctr' => 0.88],
                ['week' => 'Sep 7', 'clicks' => 2, 'impressions' => 370, 'position' => 43.0, 'ctr' => 0.54],
                ['week' => 'Sep 8', 'clicks' => 1, 'impressions' => 421, 'position' => 49.8, 'ctr' => 0.24],
                ['week' => 'Sep 9', 'clicks' => 3, 'impressions' => 418, 'position' => 39.6, 'ctr' => 0.72],
                ['week' => 'Sep 10', 'clicks' => 2, 'impressions' => 328, 'position' => 41.3, 'ctr' => 0.61],
                ['week' => 'Sep 11', 'clicks' => 3, 'impressions' => 328, 'position' => 44.3, 'ctr' => 0.91],
                ['week' => 'Sep 12', 'clicks' => 1, 'impressions' => 235, 'position' => 40.3, 'ctr' => 0.43],
                ['week' => 'Sep 13', 'clicks' => 1, 'impressions' => 218, 'position' => 38.5, 'ctr' => 0.46],
                ['week' => 'Sep 14', 'clicks' => 2, 'impressions' => 265, 'position' => 51.1, 'ctr' => 0.75],
                ['week' => 'Sep 15', 'clicks' => 2, 'impressions' => 405, 'position' => 21.3, 'ctr' => 0.49],
                ['week' => 'Sep 16', 'clicks' => 3, 'impressions' => 311, 'position' => 35.2, 'ctr' => 0.96],
                ['week' => 'Sep 17', 'clicks' => 3, 'impressions' => 303, 'position' => 39.5, 'ctr' => 0.99],
                ['week' => 'Sep 18', 'clicks' => 3, 'impressions' => 278, 'position' => 42.8, 'ctr' => 1.08],
                ['week' => 'Sep 19', 'clicks' => 4, 'impressions' => 231, 'position' => 38.9, 'ctr' => 1.73],
                ['week' => 'Sep 20', 'clicks' => 2, 'impressions' => 134, 'position' => 39.3, 'ctr' => 1.49],
                ['week' => 'Sep 21', 'clicks' => 2, 'impressions' => 246, 'position' => 32.2, 'ctr' => 0.81],
            ],
            'ga' => [
                'labels' => ['Aug 28', 'Aug 29', 'Aug 31', 'Sep 2', 'Sep 4', 'Sep 7', 'Sep 9', 'Sep 11', 'Sep 14', 'Sep 17', 'Sep 19', 'Sep 21'],
                'current' => [16, 20, 18, 25, 17, 31, 20, 18, 22, 29, 16, 10],
                'previous' => [14, 18, 16, 22, 15, 28, 18, 16, 20, 26, 14, 9],
            ],
            'gbp' => [
                'labels' => ['Aug 30', 'Aug 31', 'Sep 2', 'Sep 4', 'Sep 6', 'Sep 8', 'Sep 10', 'Sep 12', 'Sep 14', 'Sep 16', 'Sep 18', 'Sep 21'],
                'current' => [6, 7, 6, 7, 8, 8, 9, 9, 10, 10, 10, 11],
                'previous' => [5, 6, 5, 6, 7, 7, 8, 8, 9, 9, 9, 10],
                'views' => [
                    'labels' => ['Search desktop 93', 'Search mobile 68', 'Maps desktop 77', 'Maps mobile 31'],
                    'data' => [93, 68, 77, 31],
                ],
                'calls' => [0, 1, 2, 0, 1, 3, 0],
            ],
            'sources' => [
                'labels' => ['Direct 295', 'Google organic 80', 'Facebook 12', 'Bing 7', 'Other 28'],
                'data' => [295, 80, 12, 7, 28],
            ],
            'devices' => [
                'labels' => ['Mobile (37)', 'Desktop (30)'],
                'data' => [37, 30],
            ],
            'facebook' => [
                ['d' => 'Aug 28', 'v' => 0], ['d' => 'Aug 29', 'v' => 27], ['d' => 'Aug 30', 'v' => 13],
                ['d' => 'Aug 31', 'v' => 10], ['d' => 'Sep 1', 'v' => 3], ['d' => 'Sep 2', 'v' => 7],
                ['d' => 'Sep 3', 'v' => 2], ['d' => 'Sep 4', 'v' => 47], ['d' => 'Sep 5', 'v' => 4],
                ['d' => 'Sep 6', 'v' => 2], ['d' => 'Sep 7', 'v' => 1], ['d' => 'Sep 8', 'v' => 4],
                ['d' => 'Sep 9', 'v' => 1], ['d' => 'Sep 10', 'v' => 7], ['d' => 'Sep 11', 'v' => 0],
                ['d' => 'Sep 12', 'v' => 6], ['d' => 'Sep 13', 'v' => 1], ['d' => 'Sep 14', 'v' => 0],
                ['d' => 'Sep 15', 'v' => 2], ['d' => 'Sep 16', 'v' => 1], ['d' => 'Sep 17', 'v' => 6],
                ['d' => 'Sep 18', 'v' => 0], ['d' => 'Sep 19', 'v' => 6], ['d' => 'Sep 20', 'v' => 1],
                ['d' => 'Sep 21', 'v' => 4],
            ],
            'instagram' => [
                'labels' => ['Aug 28', 'Aug 28', 'Aug 31', 'Sep 1', 'Sep 3', 'Sep 5', 'Sep 7', 'Sep 9', 'Sep 11', 'Sep 14', 'Sep 16', 'Sep 18', 'Sep 21'],
                'data' => [86, 136, 53, 99, 211, 177, 21, 39, 43, 25, 19, 30, 8],
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
