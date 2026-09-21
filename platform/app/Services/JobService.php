<?php

namespace App\Services;

use App\Mail\JobDocumentMail;
use App\Mail\JobInviteMail;
use App\Mail\JobStageMail;
use App\Models\CustomerInvite;
use App\Models\EmailSend;
use App\Models\Job;
use App\Models\JobDocument;
use App\Models\JobDocumentRequest;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class JobService
{
    public function suggestPipeline(Lead $lead): ?Pipeline
    {
        $need = strtolower((string) $lead->need);
        $source = strtolower((string) $lead->source.' '.$lead->page_url);
        $slug = null;

        if ($lead->type === 'commercial') {
            $slug = match (true) {
                str_contains($need, 'coat') => 'commercial-replacement-coating',
                str_contains($need, 'maint') || str_contains($need, 'leak') => 'commercial-repair-maintenance',
                str_contains($need, 'replace') => 'commercial-replacement-coating',
                default => 'commercial-survey-bid',
            };
        } else {
            $slug = match (true) {
                str_contains($need, 'storm') || str_contains($source, 'storm') || str_contains($source, 'insurance') => 'residential-storm-insurance',
                str_contains($need, 'tarp') || str_contains($need, 'emergency') => 'emergency-tarp',
                str_contains($need, 'leak') || str_contains($need, 'repair') => 'residential-leak-repair',
                default => 'residential-retail-replacement',
            };
        }

        return Pipeline::query()->where('slug', $slug)->where('is_active', true)->first()
            ?? Pipeline::query()->where('audience', $lead->type)->where('is_active', true)->orderBy('sort')->first();
    }

    public function createFromLead(Lead $lead, array $data, ?User $actor = null): Job
    {
        if ($lead->job_id) {
            return Job::query()->findOrFail($lead->job_id);
        }

        $pipeline = Pipeline::query()->where('is_active', true)->findOrFail($data['pipeline_id']);
        $stage = $pipeline->firstStage();
        if (! $stage) {
            throw new \RuntimeException('Pipeline has no stages.');
        }

        $job = Job::query()->create([
            'number' => $this->nextNumber(),
            'lead_id' => $lead->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
            'type' => $data['type'] ?? $lead->type,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? $lead->city,
            'zip' => $data['zip'] ?? $lead->zip,
            'payment_path' => $data['payment_path'] ?? ($pipeline->slug === 'residential-storm-insurance' ? 'insurance' : 'retail'),
            'urgency' => $data['urgency'] ?? 'standard',
            'assigned_to' => $data['assigned_to'] ?? $lead->assigned_to,
            'customer_summary' => $data['customer_summary'] ?? null,
            'status' => 'open',
        ]);

        $lead->update([
            'job_id' => $job->id,
            'status' => 'active',
        ]);
        $lead->log($actor, 'job', 'Opened job '.$job->number.' on '.$pipeline->name);

        $job->log($actor, 'created', 'Job opened from lead. Stage: '.$stage->name);

        $this->seedDocumentRequests($job, $pipeline->slug);
        app(JobOpsService::class)->seedDefaultTasks($job->fresh('pipeline'));
        $this->pauseNurture($lead);

        return $job->fresh(['pipeline', 'stage', 'lead']);
    }

    public function createStandalone(array $data, ?User $actor = null): Job
    {
        $lead = null;
        if (! empty($data['lead_id'])) {
            $lead = Lead::query()->findOrFail($data['lead_id']);
            if ($lead->job_id) {
                return Job::query()->findOrFail($lead->job_id);
            }
        } elseif (! empty($data['name']) || ! empty($data['email']) || ! empty($data['phone'])) {
            $lead = Lead::query()->create([
                'name' => $data['name'] ?? 'Walk-in',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'zip' => $data['zip'] ?? null,
                'type' => $data['type'] ?? 'residential',
                'need' => $data['customer_summary'] ?? 'Opened from the office',
                'status' => 'active',
                'source' => 'office',
                'assigned_to' => $data['assigned_to'] ?? null,
            ]);
            $lead->log($actor, 'created', 'Lead opened with the job from the office');
        }

        $pipeline = Pipeline::query()->where('is_active', true)->findOrFail($data['pipeline_id']);
        $stage = $pipeline->firstStage();
        if (! $stage) {
            throw new \RuntimeException('Pipeline has no stages.');
        }

        $job = Job::query()->create([
            'number' => $this->nextNumber(),
            'lead_id' => $lead?->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
            'type' => $data['type'] ?? $lead?->type ?? $pipeline->audience,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? $lead?->city,
            'zip' => $data['zip'] ?? $lead?->zip,
            'payment_path' => $data['payment_path'] ?? 'retail',
            'urgency' => $data['urgency'] ?? 'standard',
            'assigned_to' => $data['assigned_to'] ?? $lead?->assigned_to,
            'customer_summary' => $data['customer_summary'] ?? null,
            'roof_type' => $data['roof_type'] ?? null,
            'squares' => $data['squares'] ?? null,
            'status' => 'open',
        ]);

        if ($lead) {
            $lead->update(['job_id' => $job->id, 'status' => 'active']);
        }

        $job->log($actor, 'created', 'Job opened from the office. Stage: '.$stage->name);
        $this->seedDocumentRequests($job, $pipeline->slug);
        app(JobOpsService::class)->seedDefaultTasks($job->fresh('pipeline'));

        return $job->fresh(['pipeline', 'stage', 'lead']);
    }

    public function move(Job $job, PipelineStage $stage, ?User $actor = null): Job
    {
        if ($stage->pipeline_id !== $job->pipeline_id) {
            throw new \InvalidArgumentException('Stage is not on this pipeline.');
        }

        $from = $job->stage?->name;
        $status = $job->status;
        if ($stage->outcome === 'won') {
            $status = 'won';
        } elseif ($stage->outcome === 'lost') {
            $status = 'lost';
        } elseif ($job->status !== 'on_hold') {
            $status = 'open';
        }

        $job->update([
            'stage_id' => $stage->id,
            'status' => $status,
        ]);

        if ($job->lead && in_array($status, ['won', 'lost'], true)) {
            $job->lead->update(['status' => $status]);
        }

        $job->log($actor, 'stage', ($from ?: 'Start').' → '.$stage->name, $stage->customer_visible);

        if ($stage->notify_customer) {
            $this->notifyCustomers($job, new JobStageMail($job->fresh(['stage', 'pipeline', 'lead'])));
        }

        return $job->fresh(['stage', 'pipeline', 'lead']);
    }

    public function invite(Job $job, array $data, ?User $actor = null): CustomerInvite
    {
        $invite = $job->invites()->create([
            'email' => $data['email'],
            'name' => $data['name'] ?? $job->lead?->name,
            'contact_role' => $data['contact_role'] ?? 'homeowner',
        ]);

        try {
            Mail::to($invite->email)->send(new JobInviteMail($invite));
        } catch (\Throwable) {
            // Mail may not be configured locally.
        }

        $job->log($actor, 'invite', 'Invited '.$invite->email.' to the job portal');

        return $invite;
    }

    public function acceptInvite(CustomerInvite $invite, string $password): User
    {
        $existing = User::query()->where('email', $invite->email)->first();
        if ($existing && $existing->isStaffUser()) {
            throw new \RuntimeException('That email belongs to a staff account.');
        }

        $user = $existing ?: new User(['email' => $invite->email, 'role' => 'customer']);
        $user->fill([
            'name' => $invite->name ?: ($existing?->name ?: 'Customer'),
            'role' => 'customer',
            'password' => $password,
            'is_active' => true,
        ])->save();

        $invite->job->contacts()->firstOrCreate(
            ['user_id' => $user->id],
            ['role' => $invite->contact_role]
        );

        $invite->update(['accepted_at' => now()]);
        $invite->job->log($user, 'invite_accepted', $user->name.' joined the job portal', true);

        return $user;
    }

    public function storeDocument(Job $job, UploadedFile $file, User $actor, array $data): JobDocument
    {
        $path = $file->store((string) $job->id, 'documents');
        $visibility = $data['visibility'] ?? ($actor->isCustomer() ? 'customer' : 'staff');

        $document = $job->documents()->create([
            'user_id' => $actor->id,
            'request_id' => $data['request_id'] ?? null,
            'category' => $data['category'] ?? 'other',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'visibility' => $visibility,
        ]);

        if (! empty($data['request_id'])) {
            JobDocumentRequest::query()
                ->where('id', $data['request_id'])
                ->where('job_id', $job->id)
                ->update(['fulfilled_by' => $document->id]);
        }

        $job->log($actor, 'document', $document->original_name, $visibility === 'customer');

        if ($actor->isCustomer()) {
            $this->notifyOffice($job, $document);
        }

        return $document;
    }

    public function addRequest(Job $job, array $data, ?User $actor = null): JobDocumentRequest
    {
        $request = $job->documentRequests()->create([
            'category' => $data['category'] ?? 'other',
            'label' => $data['label'],
            'required' => (bool) ($data['required'] ?? true),
        ]);

        $job->log($actor, 'doc_request', $request->label, true);

        return $request;
    }

    public function customerCanAccess(Job $job, User $user): bool
    {
        return $user->isCustomer()
            && $job->contacts()->where('user_id', $user->id)->exists();
    }

    public function staffCanAccess(User $user): bool
    {
        return $user->isStaffUser();
    }

    protected function nextNumber(): string
    {
        $prefix = 'CFR-'.now()->format('ym').'-';
        $last = Job::query()->where('number', 'like', $prefix.'%')->orderByDesc('number')->value('number');
        $n = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    protected function seedDocumentRequests(Job $job, string $pipelineSlug): void
    {
        $sets = [
            'residential-storm-insurance' => [
                ['photos', 'Photos of damage', true],
                ['insurance_dec', 'Insurance declaration page', true],
                ['adjuster_report', 'Adjuster report', false],
            ],
            'residential-retail-replacement' => [
                ['photos', 'Property photos', true],
                ['hoa', 'HOA approval (if needed)', false],
            ],
            'residential-leak-repair' => [
                ['photos', 'Photos of the leak', true],
            ],
            'emergency-tarp' => [
                ['photos', 'Photos of the leak or opening', true],
            ],
            'commercial-survey-bid' => [
                ['access', 'Site access notes', false],
                ['existing_report', 'Existing roof reports', false],
            ],
            'commercial-repair-maintenance' => [
                ['photos', 'Photos of the leak', true],
            ],
            'commercial-replacement-coating' => [
                ['photos', 'Building / roof photos', true],
            ],
        ];

        foreach ($sets[$pipelineSlug] ?? [] as [$category, $label, $required]) {
            $job->documentRequests()->create(compact('category', 'label', 'required'));
        }

        if (in_array($pipelineSlug, [
            'residential-storm-insurance',
            'residential-retail-replacement',
            'commercial-replacement-coating',
        ], true)) {
            $job->documentRequests()->create([
                'category' => 'contract',
                'label' => 'Signed contract',
                'required' => false,
            ]);
        }
    }

    protected function pauseNurture(Lead $lead): void
    {
        EmailSend::query()
            ->where('lead_id', $lead->id)
            ->where('status', 'scheduled')
            ->update(['status' => 'skipped', 'error' => 'Paused — active job']);
    }

    protected function notifyCustomers(Job $job, object $mailable): void
    {
        $job->loadMissing('contacts.user');
        foreach ($job->contacts as $contact) {
            if ($contact->user?->email) {
                try {
                    Mail::to($contact->user->email)->send(clone $mailable);
                } catch (\Throwable) {
                }
            }
        }
    }

    protected function notifyOffice(Job $job, JobDocument $document): void
    {
        $raw = Setting::get('notify_emails', config('mail.from.address'));
        $emails = array_values(array_filter(array_map('trim', explode(',', (string) $raw))));

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new JobDocumentMail($job, $document));
            } catch (\Throwable) {
            }
        }
    }
}
