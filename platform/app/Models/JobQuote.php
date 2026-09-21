<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobQuote extends Model
{
    public const STATUSES = ['draft', 'sent', 'approved', 'declined'];

    protected $fillable = [
        'job_id', 'title', 'status', 'subtotal', 'tax', 'total', 'notes', 'sent_at', 'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(JobQuoteItem::class, 'quote_id')->orderBy('sort');
    }

    public function recalculate(): void
    {
        $subtotal = (float) $this->items()->sum('amount');
        $this->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + (float) $this->tax,
        ]);
    }
}
