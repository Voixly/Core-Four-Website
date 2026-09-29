<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const STATUSES = ['new', 'contacted', 'inspected', 'bid', 'won', 'lost', 'active'];

    protected $fillable = [
        'name', 'email', 'phone', 'zip', 'city', 'type', 'need',
        'status', 'source', 'page_url', 'assigned_to', 'notes', 'job_id',
    ];

    public function typeLabel(): string
    {
        return match ($this->type) {
            'coatings' => 'Commercial coatings',
            'commercial' => 'Commercial',
            default => 'Residential',
        };
    }

    public function scopeExceptProspects($query)
    {
        return $query->where('source', '!=', 'prospect');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LeadEvent::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function log(?User $user, string $event, ?string $body = null): LeadEvent
    {
        return $this->events()->create([
            'user_id' => $user?->id,
            'event' => $event,
            'body' => $body,
        ]);
    }

    public function emailConversation(): ?Conversation
    {
        return $this->hasMany(Conversation::class)
            ->where('channel', 'email')
            ->latest('last_message_at')
            ->first();
    }

    public function ensureReplyToken(): string
    {
        if ($this->reply_token) {
            return $this->reply_token;
        }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $token = bin2hex(random_bytes(8));
            try {
                $this->forceFill(['reply_token' => $token])->save();

                return $token;
            } catch (QueryException $e) {
                $this->refresh();
                if ($this->reply_token) {
                    return $this->reply_token;
                }
                if (! str_contains($e->getMessage(), 'reply_token')) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Could not save a reply token.');
    }

    public function replyAddress(): ?string
    {
        if (! $this->exists) {
            return null;
        }

        $domain = trim((string) config('mail.inbound_domain'));
        if ($domain === '') {
            return null;
        }

        return 'r+'.$this->ensureReplyToken().'@'.$domain;
    }
}
