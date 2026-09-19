<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'type', 'metro', 'state'];

    public function path(): string
    {
        $prefix = $this->type === 'commercial' ? 'commercial' : 'residential';

        return $this->slug === 'tx'
            ? "/{$prefix}-roofing-in-tx/"
            : "/{$prefix}-roofing-in-{$this->slug}-tx/";
    }
}
