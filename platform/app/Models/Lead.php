<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const STATUSES = ['new', 'contacted', 'inspected', 'bid', 'won', 'lost'];

    protected $fillable = [
        'name', 'email', 'phone', 'zip', 'city', 'type', 'need',
        'status', 'source', 'page_url', 'assigned_to', 'notes',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LeadEvent::class)->latest();
    }

    public function log(?User $user, string $event, ?string $body = null): LeadEvent
    {
        return $this->events()->create([
            'user_id' => $user?->id,
            'event' => $event,
            'body' => $body,
        ]);
    }
}
