<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $resCities = City::query()->where('type', 'residential')->where('metro', 'Houston')->orderBy('name')->get();
        $commCities = City::query()->where('type', 'commercial')->where('metro', 'Houston')->orderBy('name')->limit(12)->get();

        return view('public.home', compact('resCities', 'commCities'));
    }

    public function residential(): View
    {
        $cities = City::query()->where('type', 'residential')->orderBy('name')->get();

        return view('public.residential', compact('cities'));
    }

    public function commercial(): View
    {
        $cities = City::query()->where('type', 'commercial')->orderBy('metro')->orderBy('name')->get();

        return view('public.commercial', compact('cities'));
    }

    public function insurance(): View
    {
        return view('public.insurance');
    }

    public function financing(): View
    {
        return view('public.financing');
    }

    public function emergency(): View
    {
        return view('public.emergency');
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function serviceAreas(): View
    {
        $residential = City::query()->where('type', 'residential')->orderBy('name')->get();
        $commercial = City::query()->where('type', 'commercial')->orderBy('metro')->orderBy('name')->get();

        return view('public.service-areas', compact('residential', 'commercial'));
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function thanks(): View
    {
        return view('public.thanks');
    }

    public function privacy(): View
    {
        return view('public.legal', [
            'title' => 'Privacy Policy',
            'heading' => 'Privacy Policy',
        ]);
    }

    public function terms(): View
    {
        return view('public.legal', [
            'title' => 'Terms of Use',
            'heading' => 'Terms of Use',
        ]);
    }

    public function guides(): View
    {
        $guides = Guide::query()->where('is_active', true)->orderBy('title')->get();

        return view('public.guides', compact('guides'));
    }

    public function guide(string $slug): View
    {
        $guide = Guide::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('public.guide', compact('guide'));
    }

    public function material(string $slug): View
    {
        $materials = $this->materials();
        abort_unless(isset($materials[$slug]), 404);
        $material = $materials[$slug];

        return view('public.material', compact('material', 'slug'));
    }

    /**
     * Render a page from the structure captured off the previous site.
     */
    public function betheme(string $slug): View
    {
        $page = \App\Support\Betheme::page($slug);
        abort_unless($page, 404);

        return view('public.betheme', compact('page', 'slug'));
    }

    public function city(Request $request, string $slug): View
    {
        $type = str_contains($request->path(), 'commercial') ? 'commercial' : 'residential';
        $city = City::query()->where('slug', $slug)->where('type', $type)->firstOrFail();

        return view('public.city', compact('city'));
    }

    public function sitemap()
    {
        $cities = City::query()->orderBy('type')->orderBy('name')->get();
        $guides = Guide::query()->where('is_active', true)->get();

        return response()
            ->view('public.sitemap', compact('cities', 'guides'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /login\nDisallow: /preview\nSitemap: ".url('/sitemap.xml')."\n";

        return response($body, 200)->header('Content-Type', 'text/plain');
    }

    protected function materials(): array
    {
        return [
            'asphalt-shingles' => [
                'title' => 'Premium Asphalt Shingle Roofing in Texas',
                'heading' => 'What Are Architectural Asphalt Shingles?',
                'type' => 'residential',
                'image' => '/images/materials/asphalt.webp',
                'ba_before' => '/images/ba/core_four_emergency_repair_before-compressed.webp',
                'ba_after' => '/images/ba/core_four_emergency_repair_after-compressed.webp',
                'blurb' => 'Modern architectural shingles offer rugged weather defense, versatile curb appeal, and cost-effectiveness for Texas homes.',
                'points' => [
                    ['title' => 'Unmatched Affordability', 'body' => 'The smartest popular investment for protecting a Houston home without overbuilding.'],
                    ['title' => 'Severe Weather Defense', 'body' => 'Advanced sealant bands, algae-resistant granules, and enhanced impact resistance.'],
                    ['title' => 'Endless Style Options', 'body' => 'Dimensional looks that satisfy HOAs and still stand up to hail and heat.'],
                ],
            ],
            'metal-roofing' => [
                'title' => 'Premium Residential Metal Roofing in Texas',
                'heading' => 'What is Standing Seam Metal Roofing?',
                'type' => 'residential',
                'image' => '/images/materials/metal.webp',
                'blurb' => 'A standing seam system is a premium architectural product — concealed fasteners and a roof you may never replace again.',
                'points' => [
                    ['title' => '50+ Year Lifespan', 'body' => 'The last roof many Texas homes will ever need.'],
                    ['title' => 'Extreme Weather Defense', 'body' => 'Concealed fasteners eliminate the leak path of exposed screw holes.'],
                    ['title' => 'Massive Energy Savings', 'body' => 'Reflective panels keep attics cooler in Houston heat.'],
                ],
            ],
            'stone-coated-steel' => [
                'title' => 'Stone-Coated Steel Roofing in Texas',
                'heading' => 'Steel strength with a tile look',
                'type' => 'residential',
                'image' => '/images/materials/steel.webp',
                'ba_before' => '/images/ba/cfr-decra-tile-before-compressed.webp',
                'ba_after' => '/images/ba/cfr-decra-tile-after-compressed.webp',
                'blurb' => 'Steel strength with a shingle or tile look — strong against hail.',
                'points' => [
                    ['title' => 'Hail Defense', 'body' => 'Stone-coated steel stands up to Texas hail better than standard shingles.'],
                    ['title' => 'Curb Appeal', 'body' => 'Tile and shake profiles without the weight of clay or wood.'],
                ],
            ],
            'synthetic-roofing' => [
                'title' => 'Synthetic Roofing in Texas',
                'heading' => 'Shake and slate looks without the weight',
                'type' => 'residential',
                'image' => '/images/materials/synthetic.webp',
                'blurb' => 'Lightweight shake and slate looks without the weight or upkeep.',
                'points' => [
                    ['title' => 'Lightweight', 'body' => 'Protects the structure without the load of real slate or shake.'],
                    ['title' => 'Low Upkeep', 'body' => 'Engineered for Texas sun without constant sealing or splitting.'],
                ],
            ],
            'tpo-roofing' => [
                'title' => 'TPO Roofing',
                'heading' => 'Thermoplastic Polyolefin',
                'type' => 'commercial',
                'image' => '/images/ba/core-four-TPO-roof-after.webp',
                'ba_before' => '/images/ba/core-four-TPO-roof-before.webp',
                'ba_after' => '/images/ba/core-four-TPO-roof-after.webp',
                'blurb' => 'White, reflective single-ply for Houston heat and energy bills.',
                'points' => [
                    ['title' => 'Energy Efficient', 'body' => 'Reflective TPO keeps commercial buildings cooler.'],
                    ['title' => 'Welded Seams', 'body' => 'Heat-welded seams for a watertight commercial system.'],
                ],
            ],
            'epdm-roofing' => [
                'title' => 'EPDM Roofing',
                'heading' => 'Rubber Roofing',
                'type' => 'commercial',
                'image' => '/images/commercial.webp',
                'blurb' => 'Durable rubber membrane for low-slope commercial buildings.',
                'points' => [
                    ['title' => 'Proven Durability', 'body' => 'Decades of commercial performance in heat and rain.'],
                ],
            ],
            'modified-bitumen' => [
                'title' => 'Modified Bitumen',
                'heading' => 'Layered commercial systems',
                'type' => 'commercial',
                'image' => '/images/commercial.webp',
                'blurb' => 'Layered commercial system for high-traffic and high-heat roofs.',
                'points' => [
                    ['title' => 'High Traffic', 'body' => 'A tough surface for roofs that see equipment and foot traffic.'],
                ],
            ],
            'built-up-roofing' => [
                'title' => 'Built-Up Roofing',
                'heading' => 'Multi-ply BUR',
                'type' => 'commercial',
                'image' => '/images/commercial.webp',
                'blurb' => 'Multi-ply BUR for warehouses and long-hold commercial assets.',
                'points' => [
                    ['title' => 'Long Hold', 'body' => 'A traditional multi-ply system for buildings you plan to keep.'],
                ],
            ],
        ];
    }
}
