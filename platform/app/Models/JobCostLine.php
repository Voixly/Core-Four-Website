<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCostLine extends Model
{
    public const KINDS = ['labor', 'material', 'sub', 'other'];

    protected $fillable = ['job_id', 'kind', 'label', 'amount', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
