<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Guide;
use App\Support\BlogPost;
use App\Support\CityPage;
use App\Support\PageLayout;
use App\Support\SiteSeo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $resCities = City::query()->where('type', 'residential')->where('metro', 'Houston')->orderBy('name')->get();
        $commCities = City::query()->where('type', 'commercial')->where('metro', 'Houston')->orderBy('name')->limit(12)->get();

        return view('public.home', compact('resCities', 'commCities'));
    }

    /**
     * Render a page from the layout captured off the previous site.
     */
    public function page(string $slug): View
    {
        $page = PageLayout::page($slug);
        abort_unless($page, 404);

        return view('public.page', compact('page', 'slug'));
    }

    public function city(Request $request, string $slug): View
    {
        $type = str_contains($request->path(), 'commercial') ? 'commercial' : 'residential';
        $city = City::query()->where('slug', $slug)->where('type', $type)->firstOrFail();
        $seo = CityPage::make($city);

        return view('public.city', compact('city', 'seo'));
    }

    public function thanks(): View
    {
        return view('public.thanks');
    }

    public function careers(): View
    {
        return view('public.careers', [
            'roles' => LeadFormController::ROLES,
        ]);
    }

    public function careersThanks(): View
    {
        return view('public.careers-thanks');
    }

    public function privacy(): View
    {
        return view('public.legal', [
            'title' => 'Privacy Policy',
            'heading' => 'Privacy Policy',
            'description' => 'How Core Four Roofing collects and uses inspection requests, chat, and guide signups from our Tomball office.',
        ]);
    }

    public function terms(): View
    {
        return view('public.legal', [
            'title' => 'Terms of Use',
            'heading' => 'Terms of Use',
            'description' => 'Terms for using the Core Four Roofing website, requesting an inspection, and getting follow-up from our Tomball office.',
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

    public function post(string $slug): View
    {
        $post = BlogPost::find($slug);
        abort_unless($post, 404);

        return view('public.post', compact('post'));
    }

    public function sitemap()
    {
        $pages = SiteSeo::indexablePages();
        $listed = array_flip($pages);
        $cities = City::query()->orderBy('type')->orderBy('name')->get()
            ->reject(fn (City $city) => isset($listed[$city->path()]));
        $guides = collect();
        $posts = array_values(array_filter(
            BlogPost::all(),
            fn (array $post) => ! isset($listed['/'.$post['slug'].'/'])
        ));

        return response()
            ->view('public.sitemap', compact('cities', 'guides', 'pages', 'posts'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $sitemap = SiteSeo::url('/sitemap.xml');
        $body = <<<TXT
User-agent: *
Allow: /
Disallow: /admin
Disallow: /login
Disallow: /account
Disallow: /preview
Disallow: /reviews

User-agent: Googlebot
Allow: /

User-agent: GPTBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: anthropic-ai
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Applebot-Extended
Allow: /

Sitemap: {$sitemap}

TXT;

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function llms(): Response
    {
        $lines = [
            '# Core Four Roofing',
            '> Tomball, Texas roofing contractor. Residential asphalt, metal, stone-coated steel, synthetic, tile, and slate. Commercial TPO, metal, coatings, repair, and inspections. Storm tarping and insurance documentation.',
            '',
            '- Office: 22955 State Highway 249 Suite 26, Tomball, TX 77375',
            '- Phone: '.config('app.office_phone'),
            '- Site: '.SiteSeo::url('/'),
            '',
            '## Main pages',
            '- [Home]('.SiteSeo::url('/').')',
            '- [Residential roofing]('.SiteSeo::url('/residential-roofing/').')',
            '- [Commercial roofing]('.SiteSeo::url('/commercial-roofing/').')',
            '- [Storm and emergency]('.SiteSeo::url('/storm-emergency/').')',
            '- [Insurance claims]('.SiteSeo::url('/insurance-claims/').')',
            '- [Financing]('.SiteSeo::url('/financing/').')',
            '- [Service areas]('.SiteSeo::url('/service-areas/').')',
            '- [Contact]('.SiteSeo::url('/contact-core-four-roofing/').')',
            '- [Careers]('.SiteSeo::url('/careers/').')',
            '- [Blog]('.SiteSeo::url('/blog/').')',
            '- [Guides]('.SiteSeo::url('/guides/').')',
            '',
            '## Guides',
        ];

        foreach (Guide::query()->where('is_active', true)->orderBy('title')->get() as $guide) {
            $lines[] = '- ['.$guide->title.']('.SiteSeo::url($guide->path()).')';
        }

        $lines = array_merge($lines, [
            '',
            '## Blog',
        ]);

        foreach (BlogPost::all() as $post) {
            $lines[] = '- ['.$post['title'].']('.SiteSeo::url('/'.$post['slug'].'/').')';
        }

        $lines = array_merge($lines, [
            '',
            '## City pages',
        ]);

        $cities = City::query()->orderBy('type')->orderBy('name')->get();
        foreach ($cities as $city) {
            $label = ($city->type === 'commercial' ? 'Commercial' : 'Residential').' roofing in '.$city->name.', TX';
            $lines[] = '- ['.$label.']('.SiteSeo::url($city->path()).')';
        }

        $lines[] = '';
        $lines[] = 'Use the city URL that matches the property. Do not treat the statewide hub as a substitute for the local page.';

        return response(implode("\n", $lines)."\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
