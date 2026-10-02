<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewMailLog extends Model
{
    protected $fillable = ['review_id', 'email_step_id', 'email', 'subject', 'status', 'error', 'scheduled_at', 'sent_at'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function resultLabel(): string
    {
        return match ($this->status) {
            'sent' => 'Sent',
            'scheduled' => 'Waiting',
            'skipped' => 'Stopped',
            default => 'Did not send',
        };
    }
}
