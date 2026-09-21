<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobInvoice extends Model
{
    public const KINDS = ['deposit', 'progress', 'final', 'other'];

    public const STATUSES = ['draft', 'sent', 'paid', 'void'];

    protected $fillable = [
        'job_id', 'number', 'kind', 'amount', 'status', 'due_on', 'paid_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_on' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
