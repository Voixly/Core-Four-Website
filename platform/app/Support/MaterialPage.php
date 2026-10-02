<?php

namespace App\Support;

class MaterialPage
{
    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $material): ?array
    {
        $pages = [
            'tile' => static::tile(),
            'slate' => static::slate(),
        ];

        return $pages[$material] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function tile(): array
    {
        return static::page(
            path: '/residential-roofing/tile-roofs/',
            title: 'Tile Roof Repair in Houston | Core Four',
            description: 'Concrete and clay tile repair in Houston suburbs. The leak is usually the underlayment, and walking the field breaks more tile than the storm did.',
            h1: 'Tile roof repair in Houston suburbs',
            kicker: 'Concrete and clay tile · Houston',
            lead: 'Katy, Cypress, The Woodlands, Sugar Land, and Fulshear are full of tile that still looks sharp from the street. The sheet under it is often the part that failed.',
            image: '/images/live/core_four_residential-compressed.webp',
            imageAlt: 'Residential roof worked by Core Four Roofing',
            formTitle: 'Free tile roof inspection',
            formNote: 'Tell us the address. We call the same day. We do not walk a tile roof like a shingle roof.',
            paragraphs: [
                'Tiles crack, slip, and break when a boot steps wrong. The felt or synthetic sheet beneath them cracks from heat, shrinks at the nails, and opens at the hips and valleys. Rain gets under a sound-looking tile and runs until it finds a seam in the deck. The ceiling stain does not sit under the broken tile you noticed.',
                'A few broken tiles and a sound underlayment is a repair: replace the tiles, re-bed the hips if they have opened, and fix the flashing. Underlayment that has turned to paper across the field is a replacement. Lifting the whole field means you are already most of the way to a new roof.',
                'The tile on the house may be a color nobody still makes. A repair then means salvaging tiles from a hidden slope, or accepting a patch you will see from the street. We say that before the job starts. On a full replacement we count what can be reused, including hips and ridges, which break more often than the field.',
                'Some of these houses are better served by stone-coated steel that keeps the profile and drops the weight. Some should stay tile, with a new underlayment and the original tiles back on where they survived. We will tell you which one the roof is.',
            ],
            points: [
                ['title' => 'We do not walk it like shingles', 'body' => 'Traffic stays on the right courses. Tiles we break get replaced. A bid that starts with a casual walk across the field is a bid to question.'],
                ['title' => 'The leak is under the piece that looks fine', 'body' => 'Hips, valleys, and the underlayment fail while the field still looks intact from the curb.'],
                ['title' => 'Matching is its own problem', 'body' => 'Discontinued colors get salvaged from a hidden slope, or you see the patch. We say which before we order a pallet.'],
            ],
            faqs: [
                ['q' => 'Can you repair a few broken tiles without replacing the roof?', 'a' => 'Yes, when the underlayment under them is still sound. If the sheet has failed across the field, a patch under tiles you do not lift will not hold.'],
                ['q' => 'Why not just walk the roof and look?', 'a' => 'A careless walk breaks more tile than the storm did. We keep traffic on the courses that can take it and replace what we break.'],
                ['q' => 'What if the tile color is discontinued?', 'a' => 'We salvage from a slope you do not see from the street, or we tell you the patch will show. A full replacement is the cleaner look when the street face would be a checkerboard.'],
                ['q' => 'Should a failed tile roof become stone-coated steel?', 'a' => 'When the frame should not carry another concrete roof, or the tile cannot be matched. If the tiles and the structure are sound, we put tile back.'],
            ],
            reads: [
                ['href' => '/tile-roofs-in-houston-suburbs/', 'title' => 'Tile roofs in Houston suburbs', 'body' => 'Why the underlayment, not the tile you can see, is the water barrier.'],
                ['href' => '/stone-coated-steel-instead-of-tile/', 'title' => 'Stone-coated steel instead of tile', 'body' => 'The lighter roof when the house should not carry concrete again.'],
                ['href' => '/residential-roofing/stone-coated-steel/', 'title' => 'Stone-coated steel', 'body' => 'The system we install when tile is the look and steel is the structure.'],
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function slate(): array
    {
        return static::page(
            path: '/residential-roofing/slate-roofs/',
            title: 'Slate Roof Repair in Houston | Core Four',
            description: 'Slate roof repair for older Houston houses. Copper nails, a slate hook, and no walking the field like asphalt. We say when the roof is actually finished.',
            h1: 'Slate roof repair on older Houston houses',
            kicker: 'Natural slate · Houston',
            lead: 'Slate can outlast the house. The nails, the copper valleys, and the flashing usually give up first. Treating it like a shingle roof is how the street face gets chewed up.',
            image: '/images/blog/slate-on-an-older-houston-house.jpg',
            imageAlt: 'Natural slate roof with copper flashing',
            formTitle: 'Free slate roof inspection',
            formNote: 'Tell us the address. We call the same day. Slate gets ladders and hooks, not a walk across the field.',
            paragraphs: [
                'A broken slate comes out. A new one goes in with a copper hook or a nailed strip hidden by the course above, so the face is not pierced. Copper nails are the fastener. Steel nails rust, swell, and pop the piece. We carry the traffic on ladders and hooks, and we replace what we break.',
                'The leak is often the valley or the dormer, not the slate you can see from the curb. Copper valleys crack at the fold after decades of heat. Chimney flashing pulls out of the mortar. Those are repairs. A field that is delaminating or shedding in sheets is a different conversation.',
                'New slate is a structural question before it is a style question. If the frame was sized for the original and the original is staying, repairs keep the look. If the frame is tired, stone-coated steel can keep a textured profile without asking the rafters to hold quarried stone again.',
                'This is a small part of our work on purpose. Older Houston houses and custom homes in The Woodlands and similar pockets are where real slate shows up. We will not talk you into tearing off slate that still has decades in it.',
            ],
            points: [
                ['title' => 'A hook, not a nail through the face', 'body' => 'The repair is hidden by the course above. A face nail is a new leak and a broken piece you can see from the street.'],
                ['title' => 'Copper at the valleys and the chimney', 'body' => 'Those details fail while the field still looks intact. We open the detail, not the whole slope.'],
                ['title' => 'Weight decides a replacement', 'body' => 'Another slate roof has to fit the frame. If it does not, we say so before anyone prices a quarry order.'],
            ],
            faqs: [
                ['q' => 'Can one broken slate be repaired?', 'a' => 'Yes. We pull the broken piece and set a new one with a copper hook or a hidden nail, then replace anything the access broke.'],
                ['q' => 'Do you walk a slate roof to inspect it?', 'a' => 'Not like asphalt. Ladders and hooks. A crew in ordinary boots across the field will break pieces you cannot hide.'],
                ['q' => 'When is slate actually done?', 'a' => 'When the pieces are delaminating or shedding across the field, not when one valley copper has cracked. A cracked valley is a repair.'],
                ['q' => 'What goes on if the house cannot carry new slate?', 'a' => 'Stone-coated steel can keep a textured profile at a fraction of the weight. We only go there when the frame is the limit.'],
            ],
            reads: [
                ['href' => '/slate-on-an-older-houston-house/', 'title' => 'Slate on an older Houston house', 'body' => 'How a single piece comes out and goes back without a nail through the face.'],
                ['href' => '/residential-roofing/stone-coated-steel/', 'title' => 'Stone-coated steel', 'body' => 'The lighter textured roof when the frame should not take new slate.'],
                ['href' => '/residential-roofing/roof-inspections/', 'title' => 'Roof inspections', 'body' => 'A look from the right access, with photos, before anyone talks about a tear-off.'],
            ],
        );
    }

    /**
     * @param  list<string>  $paragraphs
     * @param  list<array{title: string, body: string}>  $points
     * @param  list<array{q: string, a: string}>  $faqs
     * @param  list<array{href: string, title: string, body: string}>  $reads
     * @return array<string, mixed>
     */
    private static function page(
        string $path,
        string $title,
        string $description,
        string $h1,
        string $kicker,
        string $lead,
        string $image,
        string $imageAlt,
        string $formTitle,
        string $formNote,
        array $paragraphs,
        array $points,
        array $faqs,
        array $reads,
    ): array {
        $url = SiteSeo::url($path);
        $home = SiteSeo::url('/');

        return [
            'path' => $path,
            'title' => $title,
            'description' => $description,
            'h1' => $h1,
            'kicker' => $kicker,
            'lead' => $lead,
            'image' => $image,
            'image_alt' => $imageAlt,
            'form_title' => $formTitle,
            'form_note' => $formNote,
            'paragraphs' => $paragraphs,
            'points' => $points,
            'faqs' => $faqs,
            'reads' => $reads,
            'canonical' => $url,
            'schema' => [
                [
                    '@type' => 'WebPage',
                    '@id' => $url.'#webpage',
                    'url' => $url,
                    'name' => $title,
                    'headline' => $h1,
                    'description' => $description,
                    'inLanguage' => 'en-US',
                    'isPartOf' => ['@type' => 'WebSite', 'name' => 'Core Four Roofing', 'url' => $home],
                    'primaryImageOfPage' => SiteSeo::url($image),
                    'about' => [
                        '@type' => 'Service',
                        'name' => $h1,
                        'provider' => ['@id' => $home.'#business'],
                        'areaServed' => ['@type' => 'City', 'name' => 'Houston'],
                    ],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Residential roofing', 'item' => SiteSeo::url('/residential-roofing/')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $h1, 'item' => $url],
                    ],
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => array_map(fn (array $faq) => [
                        '@type' => 'Question',
                        'name' => $faq['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
                    ], $faqs),
                ],
            ],
        ];
    }
}
