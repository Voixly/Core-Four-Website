<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMaterial extends Model
{
    public const STATUSES = ['needed', 'ordered', 'delivered', 'installed'];

    protected $fillable = ['job_id', 'name', 'qty', 'unit', 'status', 'vendor', 'notes'];

    protected function casts(): array
    {
        return ['qty' => 'decimal:2'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
