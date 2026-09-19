<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'audience', 'filename', 'downloads', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(GuideDownload::class);
    }
}
