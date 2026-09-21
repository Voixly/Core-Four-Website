<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    protected $fillable = [
        'pipeline_id', 'name', 'customer_label', 'slug', 'sort',
        'customer_visible', 'notify_customer', 'outcome',
    ];

    protected function casts(): array
    {
        return [
            'customer_visible' => 'boolean',
            'notify_customer' => 'boolean',
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'stage_id');
    }

    public function isTerminal(): bool
    {
        return in_array($this->outcome, ['won', 'lost'], true);
    }

    public function isReviewStage(): bool
    {
        return in_array($this->slug, ['qc-warranty', 'review-shield', 'follow-up'], true);
    }
}
