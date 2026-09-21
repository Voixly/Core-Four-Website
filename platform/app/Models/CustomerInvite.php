<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerInvite extends Model
{
    protected $fillable = [
        'job_id', 'email', 'name', 'contact_role', 'token', 'expires_at', 'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CustomerInvite $invite) {
            if (! $invite->token) {
                $invite->token = Str::random(64);
            }
            if (! $invite->expires_at) {
                $invite->expires_at = now()->addDays(7);
            }
        });
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function isOpen(): bool
    {
        return $this->accepted_at === null && $this->expires_at->isFuture();
    }

    public function url(): string
    {
        return url('/account/invite/'.$this->token.'/');
    }
}
