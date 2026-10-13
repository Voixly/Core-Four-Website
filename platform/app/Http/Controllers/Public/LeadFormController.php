<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Services\LeadService;
use App\Services\OpenAiConversions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;

class LeadFormController extends Controller
{
    public const ROLES = [
        'Roofing installer',
        'Service technician',
        'Estimator',
        'Office',
    ];

    public function store(Request $request, LeadService $leads, OpenAiConversions $openai): RedirectResponse
    {
        if ($request->filled('website')) {
            return redirect()->route('thanks');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'zip' => ['nullable', 'string', 'max:16'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', 'in:residential,commercial'],
            'need' => ['required', 'string', 'max:80'],
            'source' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:600'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $notes = trim((string) ($data['message'] ?? $data['notes'] ?? ''));

        $lead = $leads->capture([
            'name' => trim($data['name'].' '.($data['last_name'] ?? '')),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'zip' => $data['zip'] ?? null,
            'city' => $data['city'] ?? null,
            'type' => $data['type'] ?? 'residential',
            'need' => $data['need'],
            'source' => $data['source'] ?? 'website',
            'page_url' => $request->headers->get('referer'),
            'notes' => $notes !== '' ? $notes : null,
        ]);

        $request->session()->forget('guide_download');
        $openai->sendLead($lead, $request);

        return redirect()->route('thanks')->with('openai_lead', [
            'id' => $lead->id,
            'user' => $openai->browserUser($lead),
        ]);
    }

    public function hiring(Request $request, LeadService $leads): RedirectResponse
    {
        if ($request->filled('website')) {
            return redirect()->route('careers.thanks');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'role' => ['required', 'in:'.implode(',', self::ROLES)],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $notes = trim((string) ($data['message'] ?? ''));

        $leads->capture([
            'name' => trim($data['name'].' '.($data['last_name'] ?? '')),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'] ?? null,
            'type' => 'residential',
            'need' => $data['role'],
            'source' => 'hiring',
            'page_url' => url('/careers/'),
            'notes' => $notes !== '' ? $notes : null,
        ]);

        return redirect()->route('careers.thanks');
    }

    public function download(Request $request, string $slug, LeadService $leads, OpenAiConversions $openai)
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
        $this->ensureGuideFile($guide);

        $request->session()->put('guide_download', $guide->slug);
        $openai->sendLead($lead, $request);

        return redirect()->route('thanks')->with('openai_lead', [
            'id' => $lead->id,
            'user' => $openai->browserUser($lead),
        ]);
    }

    public function file(Request $request, string $slug): StreamedResponse
    {
        if ($request->session()->get('guide_download') !== $slug) {
            abort(404);
        }

        $guide = Guide::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return $this->guideFile($guide);
    }

    private function ensureGuideFile(Guide $guide): void
    {
        $path = $guide->filename ?: 'guides/sample-guide.txt';
        if (! Storage::disk('guides')->exists($path)) {
            Storage::disk('guides')->put($path, $guide->title."\n\nCore Four Roofing guide. Call (281) 541-0027.\n");
        }
    }

    private function guideFile(Guide $guide): StreamedResponse
    {
        $this->ensureGuideFile($guide);
        $path = $guide->filename ?: 'guides/sample-guide.txt';
        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'txt';

        return Storage::disk('guides')->download($path, $guide->slug.'.'.$ext);
    }
}
