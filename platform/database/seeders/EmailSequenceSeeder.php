<?php

namespace Database\Seeders;

use App\Models\EmailSequence;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class EmailSequenceSeeder extends Seeder
{
    protected bool $replaceProspectSteps = false;

    public function run(): void
    {
        $this->seedResidential();
        $this->seedCommercial();
        $this->seedResidentialProspects();
        $this->seedCommercialProspects();
        $this->seedCoatingsProspects();
        $this->refreshProspectCopyOnce();
    }

    protected function seedResidential(): void
    {
        $sequence = EmailSequence::query()->firstOrCreate(
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
        $sequence = EmailSequence::query()->firstOrCreate(
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

    protected function seedResidentialProspects(): void
    {
        $sequence = EmailSequence::query()->firstOrCreate(
            ['name' => 'Residential prospect outreach'],
            [
                'audience' => 'residential',
                'is_active' => true,
                'description' => 'Sales outreach for homeowners. Free inspection, hail, insurance, timing, and financing.',
            ]
        );

        $steps = [
            [0, 'Free roof inspection in {{city}}', "Hi {{first_name}},\n\nCore Four Roofing inspects homes across Greater Houston. If your roof in {{city}} took hail, has a leak, or is worn out, we will come look at it for free and tell you repair or replacement.\n\nCall (281) 541-0027 and ask for the free inspection. If water is coming in tonight, we are on call 24/7.\n\n— Core Four Roofing"],
            [3, 'Hail damage you cannot see from the yard', "{{first_name}}, a Houston hail hit often looks fine from the driveway. The bruise is in the shingle, the flashing, and the soft metal. The ceiling stain shows up later, after the deck has been wet.\n\nWe will get on the roof in {{city}}, mark what the storm did, and put it in writing. The inspection is free. Call (281) 541-0027.\n\n— Core Four Roofing"],
            [10, 'We will meet the adjuster with you', "Hi {{first_name}},\n\nIf this is a storm claim, you do not have to walk it alone. We inspect first. If a claim makes sense, we can meet the adjuster on the roof so the scope matches the damage. A lot of Texas claims get written as a patch when the roof needs to come off.\n\nCall (281) 541-0027.\n\n— Core Four Roofing"],
            [21, 'Most replacements take 1 to 3 days', "{{first_name}}, people put the roof off because they picture a month of mess. On a normal house we tear off, dry in, and install in one to three days. You sleep in the house, and the yard is cleaned before we leave.\n\nThe first step in {{city}} is still the free inspection. Call (281) 541-0027.\n\n— Core Four Roofing"],
            [35, 'Financing if the roof cannot wait', "Hi {{first_name}},\n\nIf the roof needs to happen this season and the cash is the holdup, we can set up financing through a lending partner. You see the payment before you sign. We do not invent a rate on the porch.\n\nCall (281) 541-0027 and we will start with the free inspection.\n\n— Core Four Roofing"],
            [45, 'We can still come look this week', "{{first_name}}, the free inspection in {{city}} is still open. We look, we tell you repair or replace, and you decide.\n\nCall (281) 541-0027. If it is leaking tonight, say tarp and we will get there.\n\n— Core Four Roofing"],
        ];

        if ($this->replaceProspectSteps) {
            $sequence->update(['description' => 'Sales outreach for homeowners. Free inspection, hail, insurance, timing, and financing.']);
        }
        $this->writeSteps($sequence, $steps, $this->replaceProspectSteps);
    }

    protected function seedCommercialProspects(): void
    {
        $sequence = EmailSequence::query()->firstOrCreate(
            ['name' => 'Commercial prospect outreach'],
            [
                'audience' => 'commercial',
                'is_active' => true,
                'description' => 'Sales outreach for owners and managers. Free survey, leaks, a bid ownership can use, and after-hours work.',
            ]
        );

        $steps = [
            [0, 'Free roof survey for your {{city}} building', "Hello {{first_name}},\n\nIf you own or manage a building in {{city}}, Core Four will survey the roof at no charge. You get photos, years left, and a price ownership can approve. We cover Greater Houston, and we can work nights and weekends so tenants stay put.\n\nCall (281) 541-0027 and ask for a commercial survey.\n\n— Core Four Roofing"],
            [3, 'A tenant leak costs more than the roof', "{{first_name}}, one leak over an occupied suite costs more in lost rent and complaints than the repair. We find where the water gets in, document it, and schedule the fix around your hours.\n\nIf {{city}} is leaking now, call (281) 541-0027. We answer 24/7.\n\n— Core Four Roofing"],
            [10, 'A survey an owner can forward', "Hello {{first_name}},\n\nYou get photos of the field, edges, and drains, a remaining-life note, and a written number. TPO, metal, modified bitumen, or a repair. We name the one the building needs, so an out-of-town owner can read it without a translation.\n\nCall (281) 541-0027 to put {{city}} on the survey list.\n\n— Core Four Roofing"],
            [21, 'The same patch every storm is the tell', "{{first_name}}, a roof that gets patched after every cell is already in its replacement window. The survey tells you whether you have two years or two months, so the capital request happens on your calendar instead of during the next named storm.\n\nCall (281) 541-0027.\n\n— Core Four Roofing"],
            [35, 'We can work while the building stays open', "Hello {{first_name}},\n\nTear-off and recover can be set for night or weekend. One superintendent stays on the job. You get one phone number.\n\nIf you want that for {{city}}, call (281) 541-0027. We start with the free survey.\n\n— Core Four Roofing"],
            [45, 'Can we survey the building this month?', "{{first_name}}, the offer is a free roof survey, photos, and a bid. If the roof is fine, we will say so.\n\nCall (281) 541-0027 and we will get {{city}} on the board.\n\n— Core Four Roofing"],
        ];

        if ($this->replaceProspectSteps) {
            $sequence->update(['description' => 'Sales outreach for owners and managers. Free survey, leaks, a bid ownership can use, and after-hours work.']);
        }
        $this->writeSteps($sequence, $steps, $this->replaceProspectSteps);
    }

    protected function seedCoatingsProspects(): void
    {
        $sequence = EmailSequence::query()->firstOrCreate(
            ['name' => 'Commercial coatings prospect outreach'],
            [
                'audience' => 'commercial',
                'is_active' => true,
                'description' => 'Sales outreach for commercial coatings. Quote a restoration instead of a tear-off when the roof qualifies.',
            ]
        );

        $steps = [
            [0, 'Skip the tear-off on the {{city}} roof', "Hello {{first_name}},\n\nIf the commercial roof in {{city}} is aging and the deck is still sound, a silicone or acrylic coating can add 10 to 20 years without hauling the old roof off. Core Four does that restoration across Greater Houston.\n\nThe look is free. We confirm the roof qualifies, then we quote it. Call (281) 541-0027.\n\n— Core Four Roofing"],
            [3, 'Often half the cost of a new commercial roof', "{{first_name}}, a coating skips the tear-off, the dumpsters, and most of the labor. On a roof that qualifies, the job typically runs 50 to 70 percent less than a full replacement, and the building stays open while we apply it.\n\nCall (281) 541-0027 and ask for a coating quote in {{city}}.\n\n— Core Four Roofing"],
            [10, 'Silicone where it ponds, acrylic where it drains', "Hello {{first_name}},\n\nAcrylic is the reflective coat when water leaves the roof. Silicone stays waterproof where water sits, which is the flat-roof problem here. Either one cures into one seamless membrane, so the old seams stop being the leak path.\n\nWe will tell you which one the {{city}} roof needs. Call (281) 541-0027.\n\n— Core Four Roofing"],
            [21, 'A white roof in Houston heat', "{{first_name}}, a bright coating throws sun off the building instead of into the air conditioning. Tenants and customers stay inside. There is no tear-off crew shaking the ceiling.\n\nWant a price for {{city}}? Call (281) 541-0027.\n\n— Core Four Roofing"],
            [35, 'A 10 to 20 year warranty, when it qualifies', "Hello {{first_name}},\n\nThese systems carry manufacturer warranties of 10 to 20 years when the roof is a real candidate. If the insulation is too wet for a coating, we will say replacement. We will not sell a system that fails.\n\nCall (281) 541-0027. Financing is available if the owner wants it in this budget year.\n\n— Core Four Roofing"],
            [45, 'Want the coating number for {{city}}?', "{{first_name}}, we can get on that roof and send a quote. If a coating is the wrong tool, you hear that before anyone talks contract.\n\nCall (281) 541-0027 and ask for commercial coatings.\n\n— Core Four Roofing"],
        ];

        if ($this->replaceProspectSteps) {
            $sequence->update(['description' => 'Sales outreach for commercial coatings. Quote a restoration instead of a tear-off when the roof qualifies.']);
        }
        $this->writeSteps($sequence, $steps, $this->replaceProspectSteps);
    }

    protected function refreshProspectCopyOnce(): void
    {
        try {
            if (Setting::get('prospect_email_copy_v4')) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $this->replaceProspectSteps = true;
        $this->seedResidentialProspects();
        $this->seedCommercialProspects();
        $this->seedCoatingsProspects();
        $this->replaceProspectSteps = false;

        try {
            Setting::put('prospect_email_copy_v4', '1');
        } catch (\Throwable) {
            // The copy is still updated for this boot.
        }
    }

    protected function writeSteps(EmailSequence $sequence, array $steps, bool $replace = false): void
    {
        foreach ($steps as $index => [$delay, $subject, $body]) {
            $values = [
                'delay_days' => $delay,
                'subject' => $subject,
                'body' => $body,
                'is_active' => true,
            ];
            if ($replace) {
                $sequence->steps()->updateOrCreate(['position' => $index + 1], $values);
            } else {
                $sequence->steps()->firstOrCreate(['position' => $index + 1], $values);
            }
        }
    }
}
