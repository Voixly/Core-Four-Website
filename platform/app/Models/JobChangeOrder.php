<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobChangeOrder extends Model
{
    public const STATUSES = ['draft', 'sent', 'approved', 'declined'];

    protected $fillable = ['job_id', 'title', 'amount', 'status', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
