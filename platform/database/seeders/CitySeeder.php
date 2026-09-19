<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/landing-pages.json');
        if (! is_file($path)) {
            $path = dirname(__DIR__, 3).'/data/landing-pages.json';
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $houston = [
            'Houston', 'Pasadena', 'The Woodlands', 'Sugar Land', 'Conroe', 'Katy', 'Pearland',
            'League City', 'Missouri City', 'Baytown', 'Cypress', 'Spring', 'Tomball', 'Humble',
            'Richmond', 'Rosenberg', 'Friendswood', 'Alvin', 'Dickinson', 'Fulshear', 'Atascocita', 'Galveston',
        ];
        $austin = ['Austin', 'Round Rock', 'Georgetown', 'Cedar Park', 'Leander', 'Pflugerville', 'Manor', 'Kyle', 'Buda', 'Hutto', 'Lakeway'];

        foreach (['residential', 'commercial'] as $type) {
            foreach ($data[$type] ?? [] as $row) {
                $slug = $this->slugFromUrl($row['url']);
                $metro = 'Dallas';
                if (in_array($row['city'], $houston, true)) {
                    $metro = 'Houston';
                } elseif (in_array($row['city'], $austin, true)) {
                    $metro = 'Austin';
                } elseif (str_contains($row['city'], 'statewide')) {
                    $metro = 'Texas';
                }

                City::query()->updateOrCreate(
                    ['slug' => $slug, 'type' => $type],
                    ['name' => $row['city'], 'metro' => $metro, 'state' => 'TX']
                );
            }
        }
    }

    protected function slugFromUrl(string $url): string
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        if (preg_match('/(?:residential|commercial)-roofing-in-(.+)-tx$/', $path, $m)) {
            return $m[1];
        }
        if (preg_match('/(?:residential|commercial)-roofing-in-tx$/', $path)) {
            return 'tx';
        }

        return $path;
    }
}
