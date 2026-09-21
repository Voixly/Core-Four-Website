<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobWarranty extends Model
{
    public const KINDS = ['workmanship', 'manufacturer'];

    protected $fillable = [
        'job_id', 'kind', 'manufacturer', 'registration', 'starts_on', 'expires_on', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'expires_on' => 'date',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
