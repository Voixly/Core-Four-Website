<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NurtureMail;
use App\Models\EmailSequence;
use App\Models\EmailStep;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function index(): View
    {
        $sequences = EmailSequence::query()->with('steps')->orderBy('audience')->get();

        return view('admin.email.index', compact('sequences'));
    }

    public function edit(EmailStep $step): View
    {
        $step->load('sequence');

        return view('admin.email.edit', compact('step'));
    }

    public function update(Request $request, EmailStep $step): RedirectResponse
    {
        $data = $request->validate([
            'delay_days' => ['required', 'integer', 'min:0', 'max:400'],
            'subject' => ['required', 'string', 'max:190'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $step->update($data);

        return redirect()->route('admin.email.index')->with('success', 'Step saved.');
    }

    public function preview(EmailStep $step)
    {
        $step->loadMissing('sequence');
        $lead = new Lead([
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'city' => 'Tomball',
            'type' => $step->sequence->audience ?? 'residential',
        ]);

        return (new NurtureMail($step, $lead))->render();
    }

    public function test(Request $request, EmailStep $step): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $lead = new Lead([
            'name' => $request->user()->name,
            'email' => $data['email'],
            'city' => 'Tomball',
            'type' => $step->sequence->audience ?? 'residential',
        ]);

        Mail::to($data['email'])->send(new NurtureMail($step, $lead));

        return back()->with('success', 'Test sent to '.$data['email']);
    }
}
