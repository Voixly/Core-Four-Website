<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Maria Delgado',
                'email' => 'maria@example.com',
                'phone' => '2815550101',
                'city' => 'Cypress',
                'type' => 'commercial',
                'job' => 'Cypress warehouse dry-in',
                'stars' => 5,
                'comment' => 'Crew showed up when they said they would and the building is dry.',
                'status' => 'invited',
                'source' => 'invite',
                'google_clicked_at' => now()->subDay(),
            ],
            [
                'name' => 'James Whitaker',
                'email' => 'james@example.com',
                'phone' => '2815550144',
                'city' => 'Katy',
                'type' => 'residential',
                'job' => 'Katy leak repair',
                'stars' => 2,
                'comment' => 'Leak stain wasn’t fully cleaned.',
                'status' => 'held',
                'source' => 'invite',
            ],
            [
                'name' => 'Lisa Marburger',
                'city' => 'Tomball',
                'type' => 'residential',
                'job' => 'Shingle replacement',
                'stars' => 5,
                'status' => 'invited',
                'source' => 'public',
            ],
        ];

        foreach ($rows as $row) {
            Review::query()->updateOrCreate(
                ['name' => $row['name'], 'job' => $row['job']],
                $row + ['rated_at' => now()->subDays(2)]
            );
        }
    }
}
