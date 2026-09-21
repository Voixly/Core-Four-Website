<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAppointment extends Model
{
    public const TYPES = ['inspection', 'tarp', 'adjuster', 'install', 'qc', 'other'];

    public const STATUSES = ['scheduled', 'done', 'canceled'];

    protected $fillable = [
        'job_id', 'assigned_to', 'type', 'status', 'starts_at', 'ends_at', 'title', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function label(): string
    {
        return $this->title ?: ucfirst($this->type);
    }
}
