<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadFormController extends Controller
{
    public function store(Request $request, LeadService $leads): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'zip' => ['nullable', 'string', 'max:16'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', 'in:residential,commercial'],
            'need' => ['nullable', 'string', 'max:80'],
            'source' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['page_url'] = $request->headers->get('referer');
        $data['source'] = $data['source'] ?? 'website';

        $leads->capture($data);

        return redirect()->route('thanks');
    }

    public function download(Request $request, string $slug, LeadService $leads)
    {
        $guide = Guide::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'zip' => ['nullable', 'string', 'max:16'],
            'type' => ['nullable', 'in:residential,commercial'],
        ]);

        $data['type'] = $data['type'] ?? $guide->audience;
        $data['source'] = 'guide';
        $data['page_url'] = url('/guides/'.$guide->slug);
        $data['notes'] = 'Requested guide: '.$guide->title;

        $lead = $leads->capture($data);

        $guide->downloads()->create([
            'lead_id' => $lead->id,
            'email' => $lead->email,
        ]);
        $guide->increment('downloads');

        $path = $guide->filename ?: 'guides/sample-guide.txt';
        if (! Storage::disk('guides')->exists($path)) {
            Storage::disk('guides')->put($path, $guide->title."\n\nCore Four Roofing guide. Call (281) 541-0027.\n");
        }

        return Storage::disk('guides')->download($path, $guide->slug.'.pdf');
    }
}
