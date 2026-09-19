<?php

namespace Database\Seeders;

use App\Models\EmailSequence;
use Illuminate\Database\Seeder;

class EmailSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedResidential();
        $this->seedCommercial();
    }

    protected function seedResidential(): void
    {
        $sequence = EmailSequence::query()->updateOrCreate(
            ['name' => 'Residential 12-month nurture'],
            [
                'audience' => 'residential',
                'is_active' => true,
                'description' => 'StoryBrand homeowner sequence after a guide download or inspection request.',
            ]
        );

        $steps = [
            [0, 'Your Core Four guide + what happens next', "Hi {{first_name}},\n\nThanks for grabbing the guide. The next step is simple: a free home inspection from our Tomball crew. We look at hail hits, flashing, and attic moisture — then tell you straight whether you need a tarp, a repair, or a full replacement.\n\nBook it here or call (281) 541-0027.\n\n— Core Four Roofing"],
            [7, 'When a leak becomes a much bigger problem', "Hi {{first_name}},\n\nHouston storms do not wait. A small drip after hail can become wet decking and a mold bill if it sits. If water is coming in now, call (281) 541-0027 for a same-day tarp.\n\nIf it is not leaking yet, the inspection still matters — we document it before the next cell moves through {{city}}.\n\n— Core Four"],
            [30, 'Insurance claims without the runaround', "Hi {{first_name}},\n\nAdjusters in Texas often write a repair when the roof is at replacement. We meet them on site, speak their language, and keep the scope honest. You stay the customer — we stay the guide.\n\nWant us to walk the claim with you? Call (281) 541-0027.\n\n— Core Four"],
            [60, 'How a 1–3 day replacement actually works', "{{first_name}}, most homeowners are surprised how fast a clean replacement can be.\n\nDay 1: tear-off and dry-in. Day 2–3: shingles, flashing, and a broom-clean yard. You sleep in your house the whole time.\n\nIf you have been putting it off because of downtime, that is the point of this note.\n\n— Core Four Roofing"],
            [90, 'What neighbors in {{city}} say', "Hi {{first_name}},\n\nLisa in our north-Houston service area said the crew showed up when they said they would and left the place cleaner than they found it. Montgomery and Cody said the same thing — no invented prices, no disappearing superintendent.\n\nIf you want names of recent jobs near {{city}}, ask. We will not invent a review.\n\n— Core Four"],
            [120, 'Hail and heat check for this season', "{{first_name}}, Texas roofs take two punches: hail and attic heat. A 15-minute check now is cheaper than a surprise leak in August.\n\nWe can put {{city}} on the inspection calendar this month. Call (281) 541-0027.\n\n— Core Four"],
            [180, 'Repair, maintain, or replace?', "Hi {{first_name}},\n\nNot every roof needs to come off. If the decking is sound and the leaks are local, a repair plus ventilation can buy years. If granules are gone and the insurance scope is already there, replacement is the honest call.\n\nWe will tell you which one you are in — that is Integrity.\n\n— Core Four Roofing"],
            [240, 'Affordability without the cheap-roof trap', "{{first_name}}, Affordability is one of our four principles — it does not mean the thinnest shingle on the lot.\n\nWe can walk financing and material options so the monthly number works and the roof still lasts. Call (281) 541-0027 when you want that conversation.\n\n— Core Four"],
            [270, 'Gutters and ventilation that protect the new roof', "Hi {{first_name}},\n\nA new roof with clogged gutters or a dead attic still fails early. If we already replaced your roof, this is your reminder. If we have not, these add-ons often ride with the job at a better price.\n\n— Core Four Roofing"],
            [300, 'Before and after — and a clear next step', "{{first_name}}, the homes that look best six months later are the ones that did not wait for the next named storm.\n\nPrimary next step: book the free home inspection. (281) 541-0027.\n\n— Core Four"],
            [330, 'Lifetime workmanship, said plainly', "Hi {{first_name}},\n\nIntegrity means we are still the number you call after the check clears. Lifetime workmanship on the work we do — BBB A+, 1000+ jobs, Tomball HQ.\n\nIf something feels off on a roof we installed, call us first.\n\n— Core Four Roofing"],
            [365, 'Your annual inspection offer', "{{first_name}}, it has been a year. Heat, hail, and gutter overflow do quiet damage.\n\nThis is your annual inspection offer — same crew culture, no hard sell. Call (281) 541-0027 and we will put {{city}} on the list.\n\n— Core Four Roofing"],
        ];

        $this->writeSteps($sequence, $steps);
    }

    protected function seedCommercial(): void
    {
        $sequence = EmailSequence::query()->updateOrCreate(
            ['name' => 'Commercial 12-month nurture'],
            [
                'audience' => 'commercial',
                'is_active' => true,
                'description' => 'Shorter ROI / survey sequence for property managers and owners.',
            ]
        );

        $steps = [
            [0, 'Your scorecard and the survey next step', "Hello {{first_name}},\n\nThanks for the commercial guide. Next step is a roof survey: condition, remaining life, and a bid you can take to ownership. Night and weekend work is available so tenants stay put.\n\nRequest the survey or call (281) 541-0027.\n\n— Core Four Roofing"],
            [7, 'Downtime is the expensive part', "{{first_name}}, a slow leak over a tenant suite costs more than membrane. We plan tear-off windows around your operations.\n\nIf you have an active leak in {{city}}, call (281) 541-0027.\n\n— Core Four"],
            [30, 'Capital planning vs emergency spend', "Hello {{first_name}},\n\nMost Houston buildings we survey are 2–4 years from replacement and still being patched. A scored survey lets you budget the right year — not the storm year.\n\n— Core Four Roofing"],
            [60, 'How the bid and schedule work', "{{first_name}}, survey → written scope → material options (TPO, EPDM, mod-bit, BUR) → night/weekend install if needed. One superintendent, one number.\n\n— Core Four"],
            [90, 'Proof, not slogans', "Hello {{first_name}},\n\n1000+ jobs, BBB A+, lifetime workmanship. We can share recent commercial photos from the Houston metro — we will not invent a case study.\n\n— Core Four Roofing"],
            [120, 'Heat load on white TPO', "{{first_name}}, Houston heat is a line item. Reflective TPO often pays back on HVAC before the membrane is mid-life. Worth a look on the next survey.\n\n— Core Four"],
            [180, 'Patch program or replace?', "Hello {{first_name}},\n\nIf the core is wet, patches are a delay tactic. If it is isolated, a maintenance program is cheaper. We will say which one you have.\n\n— Core Four Roofing"],
            [240, 'Capex that ownership will approve', "{{first_name}}, we write scopes that a lender or out-of-state owner can understand: remaining life, leak risk, and a number. Ask for that packet.\n\n— Core Four"],
            [270, 'Drainage and edge metal', "Hello {{first_name}},\n\nFailed edge metal and ponding end more Houston commercial roofs than the field membrane. We check both on every survey.\n\n— Core Four Roofing"],
            [300, 'Survey CTA', "{{first_name}}, if the building in {{city}} is still on the maybe list, request the commercial roof survey. (281) 541-0027.\n\n— Core Four"],
            [330, 'Warranty and who answers the phone', "Hello {{first_name}},\n\nIntegrity: you get a living contact at Tomball HQ, not a vanishing sub. Lifetime workmanship on our install.\n\n— Core Four Roofing"],
            [365, 'Annual commercial inspection', "{{first_name}}, annual inspection offer for the {{city}} asset. Photos, score, and a one-page recommendation. Call (281) 541-0027.\n\n— Core Four Roofing"],
        ];

        $this->writeSteps($sequence, $steps);
    }

    protected function writeSteps(EmailSequence $sequence, array $steps): void
    {
        foreach ($steps as $index => [$delay, $subject, $body]) {
            $sequence->steps()->updateOrCreate(
                ['position' => $index + 1],
                [
                    'delay_days' => $delay,
                    'subject' => $subject,
                    'body' => $body,
                    'is_active' => true,
                ]
            );
        }
    }
}
