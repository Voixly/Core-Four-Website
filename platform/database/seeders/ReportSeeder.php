<?php

namespace Database\Seeders;

use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            [
                'title' => 'Performance report',
                'slug' => 'performance',
                'file' => 'index.html',
                'description' => 'Gains, GBP, website, social, search, keywords, outlook, landings, and listings — plus a PDF download.',
            ],
            [
                'title' => 'Residential ads plan',
                'slug' => 'residential-ads',
                'file' => 'ads.html',
                'description' => 'Houston-suburb Google Ads model at $2k / $5k / $10k.',
            ],
            [
                'title' => 'Review Shield',
                'slug' => 'review-shield',
                'file' => 'reviews.html',
                'description' => 'Private-first review gating before Google or Yelp.',
            ],
        ];

        foreach ($reports as $report) {
            Report::query()->updateOrCreate(['slug' => $report['slug']], $report);
        }
    }
}
