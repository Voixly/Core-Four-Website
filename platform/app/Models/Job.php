<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $table = 'roofing_jobs';

    public const STATUSES = ['open', 'on_hold', 'won', 'lost'];

    public const PAYMENT_PATHS = ['insurance', 'retail', 'financing', 'commercial_capex'];

    public const ROOF_TYPES = ['shingle', 'metal', 'tile', 'slate', 'stone_coated_steel', 'synthetic', 'tpo', 'other'];

    protected $fillable = [
        'number', 'lead_id', 'pipeline_id', 'stage_id', 'type',
        'address', 'city', 'zip', 'payment_path', 'urgency',
        'assigned_to', 'scheduled_at', 'customer_summary', 'status',
        'roof_type', 'squares', 'stories', 'pitch', 'material_system',
        'insurance_carrier', 'claim_number', 'hoa_name', 'access_notes', 'crew_name',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'squares' => 'decimal:2',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(JobContact::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(JobEvent::class)->latest();
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(JobDocumentRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(JobDocument::class)->latest();
    }

    public function invites(): HasMany
    {
        return $this->hasMany(CustomerInvite::class)->latest();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(JobAppointment::class)->orderBy('starts_at');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(JobQuote::class)->latest();
    }

    public function changeOrders(): HasMany
    {
        return $this->hasMany(JobChangeOrder::class)->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(JobInvoice::class)->latest();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(JobMaterial::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(JobTask::class)->orderBy('sort');
    }

    public function warranties(): HasMany
    {
        return $this->hasMany(JobWarranty::class)->latest();
    }

    public function costLines(): HasMany
    {
        return $this->hasMany(JobCostLine::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function photos()
    {
        return $this->documents->filter(fn (JobDocument $doc) => $doc->isImage());
    }

    public function approvedTotal(): float
    {
        return (float) $this->quotes->where('status', 'approved')->sum('total')
            + (float) $this->changeOrders->where('status', 'approved')->sum('amount');
    }

    public function invoicedTotal(): float
    {
        return (float) $this->invoices->whereIn('status', ['sent', 'paid'])->sum('amount');
    }

    public function paidTotal(): float
    {
        return (float) $this->invoices->where('status', 'paid')->sum('amount');
    }

    public function costTotal(): float
    {
        return (float) $this->costLines->sum('amount');
    }

    public function margin(): ?float
    {
        $sold = $this->approvedTotal();
        if ($sold <= 0) {
            return null;
        }

        return $sold - $this->costTotal();
    }

    public function title(): string
    {
        return $this->lead?->name ?: $this->number;
    }

    public function waitingOnDocs(): bool
    {
        return $this->documentRequests->contains(fn (JobDocumentRequest $request) => $request->required && ! $request->fulfilled_by);
    }

    public function nextRequestedDoc(): ?JobDocumentRequest
    {
        return $this->documentRequests->first(fn (JobDocumentRequest $request) => ! $request->fulfilled_by);
    }

    public function customerVisibleStage(): ?PipelineStage
    {
        if ($this->stage?->customer_visible) {
            return $this->stage;
        }

        return $this->pipeline?->stages
            ->where('customer_visible', true)
            ->where('sort', '<=', $this->stage?->sort ?? 0)
            ->sortByDesc('sort')
            ->first();
    }

    public function nextCustomerStage(): ?PipelineStage
    {
        return $this->pipeline?->stages
            ->where('customer_visible', true)
            ->where('sort', '>', $this->stage?->sort ?? 0)
            ->sortBy('sort')
            ->first();
    }

    public function log(?User $user, string $event, ?string $body = null, bool $customerVisible = false): JobEvent
    {
        return $this->events()->create([
            'user_id' => $user?->id,
            'event' => $event,
            'body' => $body,
            'customer_visible' => $customerVisible,
        ]);
    }
}
