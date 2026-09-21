<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobQuoteItem extends Model
{
    protected $fillable = ['quote_id', 'label', 'qty', 'unit', 'unit_price', 'amount', 'sort'];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(JobQuote::class, 'quote_id');
    }
}
