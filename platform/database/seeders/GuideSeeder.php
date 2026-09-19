<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        $guides = [
            [
                'title' => 'Houston Homeowner Storm Checklist',
                'slug' => 'houston-homeowner-storm-checklist',
                'excerpt' => 'What to photograph after hail, when to tarp, and how to talk to the adjuster.',
                'audience' => 'residential',
                'filename' => 'houston-homeowner-storm-checklist.txt',
            ],
            [
                'title' => 'Insurance Claim Walkthrough',
                'slug' => 'insurance-claim-walkthrough',
                'excerpt' => 'A plain-English path from first leak to signed scope — without getting lowballed.',
                'audience' => 'residential',
                'filename' => 'insurance-claim-walkthrough.txt',
            ],
            [
                'title' => 'Commercial Roof Condition Scorecard',
                'slug' => 'commercial-roof-condition-scorecard',
                'excerpt' => 'A one-page scorecard property managers can send after a survey.',
                'audience' => 'commercial',
                'filename' => 'commercial-roof-condition-scorecard.txt',
            ],
            [
                'title' => 'Suburb Replacement Timeline',
                'slug' => 'suburb-replacement-timeline',
                'excerpt' => 'How a 1–3 day Core Four replacement actually runs in Tomball, Cypress, and The Woodlands.',
                'audience' => 'residential',
                'filename' => 'suburb-replacement-timeline.txt',
            ],
        ];

        foreach ($guides as $guide) {
            Guide::query()->updateOrCreate(['slug' => $guide['slug']], $guide + ['is_active' => true]);
            if (! Storage::disk('guides')->exists($guide['filename'])) {
                Storage::disk('guides')->put(
                    $guide['filename'],
                    $guide['title']."\n\nCore Four Roofing\n22955 State Highway 249 Suite 26, Tomball, TX 77375\n(281) 541-0027\n\n".$guide['excerpt']."\n"
                );
            }
        }
    }
}
