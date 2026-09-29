<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'visitor_token', 'name', 'email', 'phone', 'page_url',
        'audience', 'status', 'assigned_to', 'lead_id', 'last_message_at',
        'channel', 'subject', 'reply_token', 'awaiting_staff',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'awaiting_staff' => 'boolean',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function isEmail(): bool
    {
        return $this->channel === 'email';
    }

    public function channelLabel(): string
    {
        return $this->isEmail() ? 'Email' : 'Live chat';
    }
}
