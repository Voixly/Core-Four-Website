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

    public function captureProspect(array $data): Lead
    {
        $email = strtolower(trim((string) $data['email']));
        $lead = Lead::query()->whereRaw('lower(email) = ?', [$email])->first();
        $fields = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?: 'Greater Houston',
            'type' => $data['type'],
            'need' => 'Prospect outreach',
            'notes' => $data['notes'] ?? null,
            'page_url' => $data['page_url'] ?? null,
        ];

        if ($lead) {
            if ($lead->source !== 'prospect') {
                $lead->log(null, 'note', 'Prospect intake matched an existing '.$lead->source.' lead. Left on the current email flow.');

                return $lead;
            }

            $lead->fill($fields);
            $lead->save();
            $lead->log(null, 'updated', 'Prospect updated from intake');
        } else {
            $lead = Lead::query()->create($fields + [
                'email' => $email,
                'status' => 'new',
                'source' => 'prospect',
            ]);
            $lead->log(null, 'created', 'Lead captured from prospect');
        }

        $this->enroll($lead);

        return $lead;
    }

    public function enroll(Lead $lead): void
    {
        $name = $this->sequenceName($lead);
        if (! $lead->email || $name === null) {
            return;
        }

        $sequence = EmailSequence::query()
            ->where('name', $name)
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

    private function sequenceName(Lead $lead): ?string
    {
        if ($lead->source === 'hiring' || ! $lead->email) {
            return null;
        }

        $commercial = $lead->type === 'commercial';

        if ($lead->source === 'prospect') {
            return $commercial ? 'Commercial prospect outreach' : 'Residential prospect outreach';
        }

        return $commercial ? 'Commercial 12-month nurture' : 'Residential 12-month nurture';
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
