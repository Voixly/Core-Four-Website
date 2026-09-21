<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobTask extends Model
{
    protected $fillable = ['job_id', 'assigned_to', 'title', 'is_done', 'sort', 'due_on'];

    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
            'due_on' => 'date',
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
}
