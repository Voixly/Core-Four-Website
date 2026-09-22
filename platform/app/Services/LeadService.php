<?php

namespace App\Services;

use App\Mail\LeadNotification;
use App\Models\EmailSequence;
use App\Models\EmailSend;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class LeadService
{
    public function capture(array $data, ?User $actor = null): Lead
    {
        $lead = Lead::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'zip' => $data['zip'] ?? null,
            'city' => $data['city'] ?? null,
            'type' => $data['type'] ?? 'residential',
            'need' => $data['need'] ?? null,
            'status' => 'new',
            'source' => $data['source'] ?? 'website',
            'page_url' => $data['page_url'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $lead->log($actor, 'created', 'Lead captured from '.$lead->source);
        $this->enroll($lead);
        $this->notifyOffice($lead);

        return $lead;
    }

    public function enroll(Lead $lead): void
    {
        if (! $lead->email || $lead->source === 'hiring') {
            return;
        }

        $sequence = EmailSequence::query()
            ->where('audience', $lead->type)
            ->where('is_active', true)
            ->first();

        if (! $sequence) {
            return;
        }

        foreach ($sequence->steps()->where('is_active', true)->get() as $step) {
            EmailSend::query()->firstOrCreate(
                ['email_step_id' => $step->id, 'lead_id' => $lead->id],
                [
                    'scheduled_at' => now()->addDays($step->delay_days),
                    'status' => 'scheduled',
                ]
            );
        }
    }

    protected function notifyOffice(Lead $lead): void
    {
        $raw = $lead->source === 'hiring'
            ? config('mail.hr_address')
            : config('mail.leads_address');
        $emails = array_values(array_filter(array_map('trim', explode(',', (string) $raw))));

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new LeadNotification($lead));
            } catch (\Throwable) {
                // Mail may not be configured on first boot.
            }
        }
    }
}
