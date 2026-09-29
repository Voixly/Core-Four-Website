<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProspectIntakeController extends Controller
{
    public function create(): View
    {
        return view('public.prospects');
    }

    public function store(Request $request, LeadService $leads): View
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['required', 'in:residential,commercial'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return view('public.prospects')->withErrors($validator);
        }

        $data = $validator->validated();
        $lead = $leads->captureProspect($data + [
            'page_url' => $request->fullUrl(),
        ]);

        $flow = $lead->source === 'prospect'
            ? ($lead->type === 'commercial' ? 'commercial prospect drip' : 'residential prospect drip')
            : 'existing lead, left on its current emails';

        return view('public.prospects', [
            'saved' => $lead->name.' is saved ('.$flow.').',
        ]);
    }
}
