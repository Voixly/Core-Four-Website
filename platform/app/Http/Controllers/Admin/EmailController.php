<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NurtureMail;
use App\Mail\ReviewInviteMail;
use App\Models\EmailSequence;
use App\Models\EmailStep;
use App\Models\Lead;
use App\Models\Review;
use App\Models\ReviewMailLog;
use App\Support\ReviewMailSchema;
use Database\Seeders\EmailSequenceSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function index(): View
    {
        $required = [
            'Residential 12-month nurture',
            'Commercial 12-month nurture',
            'Residential prospect outreach',
            'Commercial prospect outreach',
            'Commercial coatings prospect outreach',
            'Review Shield',
        ];
        try {
            (new EmailSequenceSeeder)->ensureReviewShield();
        } catch (\Throwable) {
            // The page still lists whatever emails already exist.
        }

        $present = EmailSequence::query()->whereIn('name', $required)->pluck('name');
        if ($present->count() < count($required)) {
            try {
                (new EmailSequenceSeeder)->run();
            } catch (\Throwable) {
                // Show whatever sequences already exist.
            }
        }

        $sequences = EmailSequence::query()->with('steps')->withCount('steps')->get()->sortBy(function (EmailSequence $sequence) use ($required) {
            $place = array_search($sequence->name, $required, true);

            return $place === false ? 100 : $place;
        })->values();

        return view('admin.email.index', compact('sequences'));
    }

    public function show(EmailSequence $sequence): View
    {
        $sequence->load('steps');
        $reviewMailsSent = 0;
        if (str_contains(strtolower($sequence->name), 'review')) {
            ReviewMailSchema::ensure();
            $reviewMailsSent = ReviewMailLog::query()->where('status', 'sent')->count();
        }

        return view('admin.email.show', compact('sequence', 'reviewMailsSent'));
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
        $step->loadMissing('sequence');

        if (str_contains(strtolower((string) $step->sequence?->name), 'review')) {
            return redirect()->to(route('admin.reviews.index').'#review-email-'.$step->id)->with('success', 'Email saved.');
        }

        return redirect()->to(route('admin.email.show', $step->email_sequence_id).'#step-'.$step->id)->with('success', 'Step saved.');
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

        if ($this->isReviewStep($step)) {
            return (new ReviewInviteMail($this->sampleReview(), $step))->render();
        }

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

        try {
            $message = $this->isReviewStep($step)
                ? new ReviewInviteMail($this->sampleReview($request->user()->name, $data['email']), $step)
                : new NurtureMail($step, $lead);
            Mail::to($data['email'])->send($message);
        } catch (\Throwable) {
            return back()->withErrors([
                'email' => 'The test could not be sent. Confirm Resend is set up for corefourroofing.com.',
            ]);
        }

        return back()->with('success', 'Test sent to '.$data['email']);
    }

    private function isReviewStep(EmailStep $step): bool
    {
        return str_contains(strtolower((string) $step->sequence?->name), 'review');
    }

    private function sampleReview(?string $name = null, ?string $email = null): Review
    {
        $review = new Review([
            'name' => $name ?: 'Alex Rivera',
            'email' => $email ?: 'alex@example.com',
            'city' => 'Cypress',
            'job' => 'shingle replacement',
            'type' => 'residential',
        ]);
        $review->token = 'preview';

        return $review;
    }
}
