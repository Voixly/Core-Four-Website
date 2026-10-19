<?php

namespace App\Support;

class ChatGptAdsPlan
{
    /**
     * One ChatGPT campaign for residential roofing at $1,000 a month.
     * Figures that are not labeled as OpenAI's published rules are planning ranges, not a forecast.
     *
     * @return array<string, mixed>
     */
    public static function make(): array
    {
        return [
            'meta' => [
                'title' => 'ChatGPT ads · $1,000',
                'prepared' => 'Oct 19, 2026',
                'filename' => 'core-four-chatgpt-residential-ads-1000.pdf',
                'focus' => 'Homeowners · Houston suburbs · one campaign',
            ],
            'budget' => [
                ['label' => 'Month', 'value' => '$1,000', 'note' => 'One residential campaign'],
                ['label' => 'Daily budget', 'value' => '$33', 'note' => 'Minimum daily budget is $25'],
                ['label' => 'Week cap', 'value' => '$231', 'note' => 'Seven times the daily budget'],
                ['label' => 'Day spike cap', 'value' => '$66', 'note' => 'Twice the daily budget, not more'],
            ],
            'clicks' => 'About 200–330 clicks a month if a click costs $3–$5. That is OpenAI’s published starting bid, not a Core Four result.',
            'groups' => [
                [
                    'name' => 'Leaks, hail, and tarping',
                    'job' => 'The higher-intent half. Someone already has water, hail, or a stain.',
                    'landing' => '/residential-roofing/roof-repair/',
                    'utm' => 'utm_source=chatgpt&utm_medium=cpc&utm_campaign=residential&utm_content=repair',
                    'hints' => [
                        'A homeowner near Tomball whose ceiling is stained after a storm and needs to know whether the roof is a repair or a replacement',
                        'Same-day residential tarping in Cypress, Spring, Humble, and The Woodlands when water is already coming in',
                        'Hail on a north Houston house, with a free photo inspection before anyone prices a tear-off',
                    ],
                    'ads' => [
                        [
                            'title' => 'A leaking roof in north Houston',
                            'copy' => 'Core Four traces the leak to the opening, tarps if water is coming in, and says repair when a repair will hold. Tomball shop. (281) 541-0027.',
                        ],
                        [
                            'title' => 'Hail on a Houston house',
                            'copy' => 'Photograph the slopes from the ground first. Then get a free inspection that matches the photos, before anyone talks you into a new roof.',
                        ],
                    ],
                ],
                [
                    'name' => 'Replacing the roof on a house',
                    'job' => 'The decision half. The roof is old, the tile is failing, or they want metal.',
                    'landing' => '/residential-roofing/roof-installation/',
                    'utm' => 'utm_source=chatgpt&utm_medium=cpc&utm_campaign=residential&utm_content=replace',
                    'hints' => [
                        'Replacing a failing concrete or clay tile roof on a house in The Woodlands, Katy, or Sugar Land',
                        'Standing-seam metal or stone-coated steel for a Houston-area homeowner who wants the roof done once',
                        'A Tomball crew that finishes most residential replacements in one to three days once the material is on site and the weather holds',
                    ],
                    'ads' => [
                        [
                            'title' => 'Replace the roof, not the ceiling stain',
                            'copy' => 'A patch on a finished roof wastes the deductible. Core Four photographs the deck and tells you repair or replacement before the price.',
                        ],
                        [
                            'title' => 'Tile, metal, or shingles',
                            'copy' => 'The material has to match the house and the HOA. Most replacements Core Four schedules are done in one to three days. Call (281) 541-0027.',
                        ],
                    ],
                ],
            ],
            'locations' => [
                'Houston', 'Tomball', 'The Woodlands', 'Cypress', 'Spring', 'Humble',
                'Conroe', 'Katy', 'Fulshear', 'Sugar Land', 'Missouri City', 'Richmond', 'Pearland',
            ],
            'negatives' => [
                'commercial', 'TPO', 'EPDM', 'warehouse', 'property manager',
                'jobs', 'hiring', 'salary', 'apprentice', 'DIY',
                'metal building', 'barn', 'shop', 'Austin', 'Dallas',
            ],
            'steps' => [
                [
                    'title' => 'Before the first dollar',
                    'body' => 'In Ads Manager, confirm Lead created is the conversion, fed by the pixel already on the site. Set the objective to Conversions and the billing to valid clicks (oCPC). Set the max bid at $4. Put the daily budget at $33 on one campaign. Add both ad groups, four ads, and the UTM links. Do not publish until the locations are the Houston cities, not the United States by itself.',
                ],
                [
                    'title' => 'Days 1–30 — let one campaign spend',
                    'body' => 'Do not add a second campaign. Check once a week: spend, average CPC against the $5 ceiling, clicks, and leads whose link says utm_source=chatgpt. Call those leads the same day. Pause an ad that gets clicks and no form starts.',
                ],
                [
                    'title' => 'Days 31–60 — keep the winner',
                    'body' => 'If the leads are homeowners inside the drive radius, leave the budget at $1,000 and rewrite the weaker ad group. If clicks are leaving the Houston suburbs, fix the location list before you touch the bid. If a platform (desktop, iOS, Android) clicks and never leads, turn that platform off.',
                ],
                [
                    'title' => 'Days 61–90 — raise it or stop',
                    'body' => 'Raise the daily budget only after a full month of in-area homeowner leads and a cost per lead the office can live with. If $1,000 produced no lead we can drive to, stop. Another month at the same setup will not invent demand.',
                ],
            ],
            'dont' => [
                'A Views campaign. $1,000 spent on impressions does not buy a roof appointment.',
                'The United States with no city. Excluding other countries does not keep the ads in Houston.',
                'A context hint that says “only show this in Houston.” Hints are not a location setting.',
                'Two campaigns. Each campaign needs a $25 daily budget, which would blow past $1,000.',
                'The homepage. Repair ads go to the repair page. Replacement ads go to the installation page.',
                'Commercial roofs, warehouses, and hiring. Those conversations are not this budget.',
                'Changing the objective later. OpenAI requires a new campaign for a new objective.',
                'Expecting Plus, Pro, or Business users to see the ad. OpenAI does not show ads on those plans, or to accounts identified as under 18.',
            ],
        ];
    }
}
