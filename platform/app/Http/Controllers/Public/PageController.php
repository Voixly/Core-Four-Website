<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Guide;
use App\Support\PageLayout;
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

        return view('public.city', compact('city'));
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
}
