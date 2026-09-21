<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Review extends Model
{
    public const STATUSES = ['pending', 'invited', 'held', 'recovered', 'closed'];

    protected $fillable = [
        'token', 'name', 'email', 'phone', 'city', 'type', 'job',
        'stars', 'comment', 'status', 'source', 'lead_id', 'job_id', 'assigned_to',
        'rated_at', 'google_clicked_at', 'yelp_clicked_at', 'recovery_notes',
    ];

    protected function casts(): array
    {
        return [
            'rated_at' => 'datetime',
            'google_clicked_at' => 'datetime',
            'yelp_clicked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Review $review) {
            if (! $review->token) {
                $review->token = Str::random(40);
            }
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function roofingJob(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isHappy(): bool
    {
        return (int) $this->stars >= 4;
    }

    public function publicUrl(): string
    {
        return rtrim(url('/'), '/').'/reviews/'.$this->token.'/';
    }
}
