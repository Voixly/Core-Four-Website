<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideDownload extends Model
{
    protected $fillable = ['guide_id', 'lead_id', 'email'];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
