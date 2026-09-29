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
                'description' => 'Cold homeowner outreach. Asks for an inspection, then handles leak, hard-sell, insurance, and price objections.',
            ]
        );

        $steps = [
            [0, 'Roof inspection in {{city}}', "Hi {{first_name}},\n\nIf the roof on your {{city}} home has hail marks, a leak, or is just old, we will come look and tell you repair or replacement. We have not been on it yet.\n\nCall (281) 541-0027 or book the inspection: https://corefourroofing.com/contact-core-four-roofing/\n\nCore Four Roofing. Greater Houston, from our Tomball shop.\n\n— Core Four Roofing"],
            [3, 'The ceiling stain means the leak is old', "{{first_name}}, in this heat a small leak sits in the attic and becomes soft decking before you see a stain. The stain is late, not early.\n\nIf water is coming in now, call (281) 541-0027 and say tarp. If it is quiet, the inspection still documents the roof before the next storm.\n\n— Core Four"],
            [10, 'The visit is an answer, not a pitch', "Hi {{first_name}},\n\nHere is the {{city}} inspection: we walk the field and flashing, check the attic when we can get in, and say repair or replace. If a repair will hold, that is what we recommend.\n\nCall (281) 541-0027.\n\n— Core Four Roofing"],
            [21, 'You do not have to file a claim first', "{{first_name}}, a lot of homeowners wait on the insurance company and get a repair check for a roof that needs to come off.\n\nWe can look first. If a claim makes sense, we can meet the adjuster. You stay in charge of the decision.\n\nCall (281) 541-0027.\n\n— Core Four"],
            [35, 'If price is why this is sitting', "Hi {{first_name}},\n\nWe can walk materials and financing so the monthly number works and the roof still lasts. We will not sell you the thinnest shingle on the lot.\n\nCall (281) 541-0027 or use https://corefourroofing.com/contact-core-four-roofing/\n\n— Core Four Roofing"],
            [45, 'Should I close your file?', "{{first_name}}, this is the last note.\n\nReply inspection, or reply stop. If the {{city}} house needs us, call (281) 541-0027.\n\n— Core Four Roofing"],
        ];

        if ($this->replaceProspectSteps) {
            $sequence->update(['description' => 'Cold homeowner outreach. Asks for an inspection, then handles leak, hard-sell, insurance, and price objections.']);
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
                'description' => 'Cold outreach for owners and managers. Asks for a roof survey, then handles leaks, scope, budget timing, and downtime.',
            ]
        );

        $steps = [
            [0, 'Roof survey for the {{city}} building', "Hello {{first_name}},\n\nIf you own or manage a building in {{city}}, we will survey the roof and send photos, years left, and a number ownership can read. We have not been on it.\n\nCall (281) 541-0027 or request the survey: https://corefourroofing.com/contact-core-four-roofing/\n\nCore Four Roofing. Greater Houston, from Tomball.\n\n— Core Four Roofing"],
            [3, 'A leak costs more than the membrane', "{{first_name}}, water over a tenant suite costs more in downtime than the repair. We have not inspected this roof.\n\nIf it is already leaking in {{city}}, call (281) 541-0027. We schedule the work around the tenants when the building can stay open.\n\n— Core Four"],
            [10, 'What you actually get back', "Hello {{first_name}},\n\nPhotos of the field, edges, and drains. A remaining-life note. A price. We name the system the deck can hold, whether that is TPO, metal, or a repair. You do not get a slide deck and a shrug.\n\nCall (281) 541-0027.\n\n— Core Four Roofing"],
            [21, 'Pick the year before a storm does', "{{first_name}}, patched roofs get replaced in the expensive year, which is the storm year. A survey lets ownership choose a calmer one.\n\nIf {{city}} should be on that calendar, call (281) 541-0027.\n\n— Core Four"],
            [35, 'The building does not have to close', "Hello {{first_name}},\n\nNight and weekend work is available so a Greater Houston property stays open. One superintendent. One number.\n\nCall (281) 541-0027.\n\n— Core Four Roofing"],
            [45, 'Survey, or should I stop?', "{{first_name}}, this is the last note.\n\nReply survey, or reply stop. If the {{city}} building still needs the photos and the number, call (281) 541-0027.\n\n— Core Four Roofing"],
        ];

        if ($this->replaceProspectSteps) {
            $sequence->update(['description' => 'Cold outreach for owners and managers. Asks for a roof survey, then handles leaks, scope, budget timing, and downtime.']);
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
                'description' => 'Cold coatings outreach. Asks for a moisture check, and only sells a coating when the roof is dry.',
            ]
        );

        $steps = [
            [0, 'A coating for {{city}}, if the roof is dry', "Hello {{first_name}},\n\nA silicone or acrylic coating can keep a commercial roof in {{city}} and skip a tear-off. Only if the insulation is dry. We have not been on this building, so the first step is a moisture check, not a contract.\n\nCall (281) 541-0027 or ask for the coating check: https://corefourroofing.com/contact-core-four-roofing/\n\nCore Four Roofing. Greater Houston, from Tomball.\n\n— Core Four Roofing"],
            [3, 'Do not coat a wet roof', "{{first_name}}, a coating over wet insulation fails, and then you pay for a replacement anyway. A dry field can gain years. A wet one cannot.\n\nWe have not inspected this roof. The visit is how we tell them apart. Call (281) 541-0027.\n\n— Core Four"],
            [10, 'What the moisture check includes', "Hello {{first_name}},\n\nWe look for trapped water, then seams, drains, and edge metal. After that we say silicone, acrylic, or replace. If the deck cannot hold a coating, we will not sell you one.\n\nCall (281) 541-0027.\n\n— Core Four Roofing"],
            [21, 'Less downtime, when the roof qualifies', "{{first_name}}, owners choose a coating so the building stays open and the surface reflects Houston heat. Neither matters on a wet roof.\n\nThe check tells you which building you have. Night and weekend visits are available. Call (281) 541-0027.\n\n— Core Four"],
            [35, 'A number an owner can approve', "Hello {{first_name}},\n\nIf the {{city}} roof qualifies, you get a scope and a price. If it does not, you get a replacement recommendation and we stop talking about coating.\n\nCall (281) 541-0027 or use https://corefourroofing.com/contact-core-four-roofing/\n\n— Core Four Roofing"],
            [45, 'Coating check, or stop?', "{{first_name}}, this is the last note.\n\nReply check, or reply stop. If you want the moisture reading and a coating price for {{city}}, call (281) 541-0027.\n\n— Core Four Roofing"],
        ];

        if ($this->replaceProspectSteps) {
            $sequence->update(['description' => 'Cold coatings outreach. Asks for a moisture check, and only sells a coating when the roof is dry.']);
        }
        $this->writeSteps($sequence, $steps, $this->replaceProspectSteps);
    }

    protected function refreshProspectCopyOnce(): void
    {
        try {
            if (Setting::get('prospect_email_copy_v2')) {
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
            Setting::put('prospect_email_copy_v2', '1');
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
