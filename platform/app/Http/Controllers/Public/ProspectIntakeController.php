<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProspectIntakeController extends Controller
{
    public function create(Request $request): View
    {
        $this->guard($request);

        return view('public.prospects');
    }

    public function store(Request $request, LeadService $leads): RedirectResponse
    {
        $this->guard($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['required', 'in:residential,commercial'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead = $leads->captureProspect($data + [
            'page_url' => $request->fullUrl(),
        ]);

        $flow = $lead->source === 'prospect'
            ? ($lead->type === 'commercial' ? 'commercial prospect drip' : 'residential prospect drip')
            : 'existing lead, left on its current emails';

        return back()->with('success', $lead->name.' is saved ('.$flow.').');
    }

    private function guard(Request $request): void
    {
        $expected = (string) config('services.prospect.key');
        if ($expected === '') {
            abort(404);
        }

        if ($request->isMethod('POST') && ! hash_equals($expected, (string) $request->input('key'))) {
            abort(404);
        }
    }
}
