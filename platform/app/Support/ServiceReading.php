<?php

namespace App\Support;

class ServiceReading
{
    /**
     * Articles and specialty pages that belong under a captured service page.
     *
     * @return list<array{href: string, title: string, body: string}>
     */
    public static function forSlug(string $slug): array
    {
        return match ($slug) {
            'residential-roofing--metal-roofs' => [
                ['href' => '/standing-seam-on-a-houston-home/', 'title' => 'Standing seam on a Houston home', 'body' => 'Hidden clips, a solid deck, and why screw-down metal is a different roof.'],
                ['href' => '/screws-on-a-porch-metal-roof/', 'title' => 'Screws on a porch metal roof', 'body' => 'The rubber washers on a patio roof are the leak. The house should not be built that way.'],
                ['href' => '/metal-or-asphalt-on-a-texas-house/', 'title' => 'Metal or asphalt on a Texas house', 'body' => 'Heat, hail, and the honest choice for the years you expect to stay.'],
            ],
            'residential-roofing--stone-coated-steel' => [
                ['href' => '/stone-coated-steel-instead-of-tile/', 'title' => 'Stone-coated steel instead of tile', 'body' => 'Same profile from the curb, a fraction of the weight on the rafters.'],
                ['href' => '/residential-roofing/tile-roofs/', 'title' => 'Tile roof repair', 'body' => 'When the tile can stay, and when the underlayment means it cannot.'],
                ['href' => '/tile-roofs-in-houston-suburbs/', 'title' => 'Tile roofs in Houston suburbs', 'body' => 'Katy, Cypress, and the leak that starts under a tile that still looks fine.'],
            ],
            'residential-roofing--roof-repair' => [
                ['href' => '/residential-roofing/tile-roofs/', 'title' => 'Tile roof repair', 'body' => 'Broken tiles and failed underlayment on Houston suburb houses.'],
                ['href' => '/kickout-flashing-at-the-sidewall/', 'title' => 'Kickout flashing at the sidewall', 'body' => 'The small metal turn that keeps rain out from behind the siding.'],
                ['href' => '/skylight-leak-that-is-the-flashing/', 'title' => 'When the skylight leak is the flashing', 'body' => 'New glass in the same hole does not fix a bad curb.'],
            ],
            'residential-roofing' => [
                ['href' => '/residential-roofing/tile-roofs/', 'title' => 'Tile roofs', 'body' => 'Repair and replacement for concrete and clay tile in the Houston suburbs.'],
                ['href' => '/residential-roofing/slate-roofs/', 'title' => 'Slate roofs', 'body' => 'Hook repairs and copper details on older Houston houses.'],
                ['href' => '/residential-roofing/metal-roofs/', 'title' => 'Metal roofs', 'body' => 'Standing seam for the house. Screw-down stays on the porch.'],
            ],
            'commercial-roofing--repair-preventative-maintenance' => [
                ['href' => '/ponding-water-on-a-low-slope-roof/', 'title' => 'Ponding water on a low-slope roof', 'body' => 'Water still there two days later, even when the drains are open.'],
                ['href' => '/tpo-seams-in-texas-heat/', 'title' => 'TPO seams in Texas heat', 'body' => 'The weld opens while the white field still looks new.'],
                ['href' => '/parapet-coping-that-leaks/', 'title' => 'Parapet coping that leaks', 'body' => 'The stain along the outside wall starts at the cap, not in the middle of the roof.'],
            ],
            'commercial-roofing--roof-replacement-installation' => [
                ['href' => '/tpo-seams-in-texas-heat/', 'title' => 'TPO seams in Texas heat', 'body' => 'When a weld repair is enough, and when the sheet is finished.'],
                ['href' => '/ponding-water-on-a-low-slope-roof/', 'title' => 'Ponding on a low-slope roof', 'body' => 'A new membrane over the same low spot reproduces the pond.'],
                ['href' => '/commercial-roofing-in-humble-tx/', 'title' => 'Commercial roofing in Humble', 'body' => 'The commercial repair search we already show up for on the east side.'],
            ],
            default => [],
        };
    }
}
