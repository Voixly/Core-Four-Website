<?php

namespace App\Services;

use App\Mail\ReviewInviteMail;
use App\Mail\ReviewRecoveryMail;
use App\Models\EmailSequence;
use App\Models\EmailStep;
use App\Models\Lead;
use App\Models\Review;
use App\Models\ReviewMailLog;
use App\Models\Setting;
use App\Models\User;
use App\Support\ReviewMailSchema;
use Database\Seeders\EmailSequenceSeeder;
use Illuminate\Support\Facades\Mail;

class ReviewService
{
    public function invite(array $data, ?User $actor = null, bool $emailCustomer = false): Review
    {
        $review = Review::query()->create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'type' => $data['type'] ?? 'residential',
            'job' => $data['job'] ?? null,
            'status' => 'pending',
            'source' => $data['source'] ?? 'invite',
            'lead_id' => $data['lead_id'] ?? null,
            'job_id' => $data['job_id'] ?? null,
        ]);

        if ($emailCustomer) {
            $this->emailCustomer($review);
        }

        return $review;
    }

    public function inviteFromLead(Lead $lead, bool $emailCustomer = false): Review
    {
        $existing = Review::query()->where('lead_id', $lead->id)->whereNull('stars')->latest()->first();
        if ($existing) {
            if ($emailCustomer) {
                $this->emailCustomer($existing);
            }

            return $existing;
        }

        return $this->invite([
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'city' => $lead->city,
            'type' => $lead->type,
            'job' => $lead->need,
            'source' => 'lead',
            'lead_id' => $lead->id,
            'job_id' => $lead->job_id,
        ], null, $emailCustomer);
    }

    public function rate(Review $review, array $data): Review
    {
        $stars = (int) $data['stars'];

        $review->fill([
            'name' => $data['name'] ?? $review->name,
            'email' => $data['email'] ?? $review->email,
            'phone' => $data['phone'] ?? $review->phone,
            'city' => $data['city'] ?? $review->city,
            'type' => $data['type'] ?? $review->type,
            'stars' => $stars,
            'comment' => $data['comment'] ?? $review->comment,
            'status' => $stars >= 4 ? 'invited' : 'held',
            'rated_at' => now(),
        ])->save();

        if ($stars <= 3) {
            $this->notifyOffice($review);
        }

        return $review->fresh();
    }

    /**
     * Send the Review Shield rating email and write it to the log.
     * sent, failed, or skipped when there is no address.
     */
    public function emailCustomer(Review $review): string
    {
        if (! $review->email) {
            $review->emailResult = 'skipped';

            return 'skipped';
        }

        ReviewMailSchema::ensure();
        $step = $this->reviewStep();
        $mail = new ReviewInviteMail($review, $step);

        try {
            Mail::to($review->email)->send($mail);
            ReviewMailLog::query()->create([
                'review_id' => $review->id,
                'email' => $review->email,
                'subject' => $mail->subjectLine(),
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            $review->emailResult = 'sent';

            return 'sent';
        } catch (\Throwable $e) {
            ReviewMailLog::query()->create([
                'review_id' => $review->id,
                'email' => $review->email,
                'subject' => $mail->subjectLine(),
                'status' => 'failed',
                'error' => mb_substr($e->getMessage(), 0, 500),
                'sent_at' => now(),
            ]);
            $review->emailResult = 'failed';

            return 'failed';
        }
    }

    private function reviewStep(): ?EmailStep
    {
        $sequence = EmailSequence::query()->where('name', 'Review Shield')->first();
        if (! $sequence) {
            try {
                (new EmailSequenceSeeder)->run();
            } catch (\Throwable) {
                return null;
            }
            $sequence = EmailSequence::query()->where('name', 'Review Shield')->first();
        }

        return $sequence?->steps()->where('is_active', true)->orderBy('position')->first();
    }

    public function googleUrl(): string
    {
        return (string) Setting::get(
            'google_review_url',
            'https://www.google.com/search?q=Core+Four+Roofing+Tomball+TX+reviews'
        );
    }

    public function yelpUrl(): string
    {
        return (string) Setting::get(
            'yelp_review_url',
            'https://www.yelp.com/biz/core-four-roofing-tomball-3'
        );
    }

    protected function notifyOffice(Review $review): void
    {
        $raw = Setting::get('notify_emails', config('mail.from.address'));
        $emails = array_values(array_filter(array_map('trim', explode(',', (string) $raw))));

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new ReviewRecoveryMail($review));
            } catch (\Throwable) {
                // Mail may not be configured locally.
            }
        }
    }
}
