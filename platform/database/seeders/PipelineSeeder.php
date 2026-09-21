<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        $pipelines = [
            [
                'name' => 'Residential storm / insurance',
                'slug' => 'residential-storm-insurance',
                'audience' => 'residential',
                'sort' => 1,
                'stages' => [
                    ['Callback', 'We got your request', 'callback', false, false, 'open'],
                    ['Inspection scheduled', 'Inspection is on the calendar', 'inspection-scheduled', true, true, 'open'],
                    ['Inspected', 'Inspection is complete', 'inspected', true, true, 'open'],
                    ['Tarp if needed', 'We stopped the leak', 'tarp', true, true, 'open'],
                    ['Claim documented', 'Claim photos are with your file', 'claim-documented', true, false, 'open'],
                    ['Adjuster meeting', 'Adjuster meeting is set', 'adjuster-meeting', true, true, 'open'],
                    ['Scope approved', 'Insurance scope is approved', 'scope-approved', true, true, 'open'],
                    ['Contract', 'Contract is ready to sign', 'contract', true, true, 'open'],
                    ['Materials / permits', 'Materials and permits are in motion', 'materials-permits', true, false, 'open'],
                    ['Install scheduled', 'Install day is booked', 'install-scheduled', true, true, 'open'],
                    ['Dry-in', 'The roof is dried in', 'dry-in', true, true, 'open'],
                    ['Install complete', 'The new roof is on', 'install-complete', true, true, 'open'],
                    ['QC / warranty', 'Final walkthrough and warranty', 'qc-warranty', true, true, 'open'],
                    ['Review Shield', 'How did we do?', 'review-shield', true, false, 'open'],
                    ['Closed won', 'Job complete', 'closed-won', true, true, 'won'],
                    ['Closed lost', 'This job did not move forward', 'closed-lost', false, false, 'lost'],
                ],
            ],
            [
                'name' => 'Residential retail replacement',
                'slug' => 'residential-retail-replacement',
                'audience' => 'residential',
                'sort' => 2,
                'stages' => [
                    ['Callback', 'We got your request', 'callback', false, false, 'open'],
                    ['Consultation', 'Consultation is scheduled', 'consultation', true, true, 'open'],
                    ['Inspected', 'Inspection is complete', 'inspected', true, true, 'open'],
                    ['Repair vs replace', 'We are recommending a path', 'repair-vs-replace', false, false, 'open'],
                    ['Estimate / materials', 'Estimate and materials are ready', 'estimate', true, true, 'open'],
                    ['Financing', 'Financing options are open', 'financing', true, false, 'open'],
                    ['Contract', 'Contract is ready to sign', 'contract', true, true, 'open'],
                    ['Materials / permits', 'Materials and permits are in motion', 'materials-permits', true, false, 'open'],
                    ['Install scheduled', 'Install day is booked', 'install-scheduled', true, true, 'open'],
                    ['Dry-in', 'The roof is dried in', 'dry-in', true, true, 'open'],
                    ['Install complete', 'The new roof is on', 'install-complete', true, true, 'open'],
                    ['QC / warranty', 'Final walkthrough and warranty', 'qc-warranty', true, true, 'open'],
                    ['Review Shield', 'How did we do?', 'review-shield', true, false, 'open'],
                    ['Closed won', 'Job complete', 'closed-won', true, true, 'won'],
                    ['Closed lost', 'This job did not move forward', 'closed-lost', false, false, 'lost'],
                ],
            ],
            [
                'name' => 'Residential leak repair',
                'slug' => 'residential-leak-repair',
                'audience' => 'residential',
                'sort' => 3,
                'stages' => [
                    ['Callback', 'We got your request', 'callback', false, false, 'open'],
                    ['Inspection', 'Inspection is scheduled', 'inspection', true, true, 'open'],
                    ['Quote', 'Repair quote is ready', 'quote', true, true, 'open'],
                    ['Scheduled', 'Repair day is booked', 'scheduled', true, true, 'open'],
                    ['Complete', 'Repair is finished', 'complete', true, true, 'open'],
                    ['Follow-up', 'We are checking the repair', 'follow-up', true, true, 'open'],
                    ['Review Shield', 'How did we do?', 'review-shield', true, false, 'open'],
                    ['Closed won', 'Job complete', 'closed-won', true, true, 'won'],
                    ['Closed lost', 'This job did not move forward', 'closed-lost', false, false, 'lost'],
                ],
            ],
            [
                'name' => 'Emergency tarp',
                'slug' => 'emergency-tarp',
                'audience' => 'residential',
                'sort' => 4,
                'stages' => [
                    ['Intake', 'Emergency request received', 'intake', true, true, 'open'],
                    ['Dispatched', 'A crew is on the way', 'dispatched', true, true, 'open'],
                    ['Tarp complete', 'The tarp is on', 'tarp-complete', true, true, 'open'],
                    ['Follow-up inspection', 'Follow-up inspection is next', 'follow-up', true, true, 'open'],
                    ['Converted', 'Moving to a full job', 'converted', false, false, 'won'],
                    ['Tarp only', 'Tarp-only close', 'tarp-only', true, true, 'won'],
                    ['Closed lost', 'Could not dispatch', 'closed-lost', false, false, 'lost'],
                ],
            ],
            [
                'name' => 'Commercial survey and bid',
                'slug' => 'commercial-survey-bid',
                'audience' => 'commercial',
                'sort' => 5,
                'stages' => [
                    ['Qualify', 'We are confirming the property', 'qualify', false, false, 'open'],
                    ['Survey scheduled', 'Survey is on the calendar', 'survey-scheduled', true, true, 'open'],
                    ['Survey complete', 'Survey is finished', 'survey-complete', true, true, 'open'],
                    ['Condition report', 'Condition report is ready', 'condition-report', true, true, 'open'],
                    ['Written bid', 'Bid is with ownership', 'written-bid', true, true, 'open'],
                    ['CapEx review', 'Waiting on CapEx review', 'capex-review', true, false, 'open'],
                    ['Won — branch', 'Awarded — next job type', 'won-branch', false, true, 'won'],
                    ['Deferred re-inspect', 'Parked for a later survey', 'deferred', true, false, 'open'],
                    ['Closed lost', 'Did not award', 'closed-lost', false, false, 'lost'],
                ],
            ],
            [
                'name' => 'Commercial repair / maintenance',
                'slug' => 'commercial-repair-maintenance',
                'audience' => 'commercial',
                'sort' => 6,
                'stages' => [
                    ['Tarp if active', 'Stopping an active leak', 'tarp', true, true, 'open'],
                    ['Leak survey', 'Leak survey is scheduled', 'leak-survey', true, true, 'open'],
                    ['Quote / program', 'Quote or maintenance program is ready', 'quote', true, true, 'open'],
                    ['Scheduled', 'Work is on the calendar', 'scheduled', true, true, 'open'],
                    ['Complete', 'Repair is finished', 'complete', true, true, 'open'],
                    ['Punch list', 'Punch list items remain', 'punch-list', true, false, 'open'],
                    ['Review Shield', 'How did we do?', 'review-shield', true, false, 'open'],
                    ['Closed won', 'Job complete', 'closed-won', true, true, 'won'],
                    ['Closed lost', 'Did not move forward', 'closed-lost', false, false, 'lost'],
                ],
            ],
            [
                'name' => 'Commercial replacement / coating',
                'slug' => 'commercial-replacement-coating',
                'audience' => 'commercial',
                'sort' => 7,
                'stages' => [
                    ['Bid accepted', 'Work is awarded', 'bid-accepted', true, true, 'open'],
                    ['Permitting', 'Permits are in motion', 'permitting', true, false, 'open'],
                    ['Night / weekend schedule', 'Install window is booked', 'schedule', true, true, 'open'],
                    ['Install / coating', 'Crews are on the roof', 'install', true, true, 'open'],
                    ['Punch list', 'Punch list items remain', 'punch-list', true, false, 'open'],
                    ['QC / warranty', 'Final walkthrough and warranty', 'qc-warranty', true, true, 'open'],
                    ['Review Shield', 'How did we do?', 'review-shield', true, false, 'open'],
                    ['Closed won', 'Job complete', 'closed-won', true, true, 'won'],
                    ['Closed lost', 'Did not move forward', 'closed-lost', false, false, 'lost'],
                ],
            ],
        ];

        foreach ($pipelines as $row) {
            $stages = $row['stages'];
            unset($row['stages']);

            $pipeline = Pipeline::query()->updateOrCreate(
                ['slug' => $row['slug']],
                $row + ['is_active' => true]
            );

            foreach ($stages as $index => [$name, $customerLabel, $slug, $visible, $notify, $outcome]) {
                $pipeline->stages()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'customer_label' => $customerLabel,
                        'sort' => $index + 1,
                        'customer_visible' => $visible,
                        'notify_customer' => $notify,
                        'outcome' => $outcome,
                    ]
                );
            }
        }
    }
}
