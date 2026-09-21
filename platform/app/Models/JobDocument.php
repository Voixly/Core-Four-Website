<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobDocument extends Model
{
    protected $fillable = [
        'job_id', 'user_id', 'request_id', 'category', 'path', 'original_name', 'visibility',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(JobDocumentRequest::class, 'request_id');
    }

    public function isImage(): bool
    {
        return (bool) preg_match('/\.(jpe?g|png|webp|gif|heic)$/i', $this->original_name.' '.$this->path);
    }
}
