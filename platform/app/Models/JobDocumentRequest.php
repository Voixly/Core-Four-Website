<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobDocumentRequest extends Model
{
    protected $fillable = ['job_id', 'category', 'label', 'required', 'fulfilled_by'];

    protected function casts(): array
    {
        return ['required' => 'boolean'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function fulfillment(): BelongsTo
    {
        return $this->belongsTo(JobDocument::class, 'fulfilled_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(JobDocument::class, 'request_id');
    }

    public function isFulfilled(): bool
    {
        return (bool) $this->fulfilled_by;
    }
}
