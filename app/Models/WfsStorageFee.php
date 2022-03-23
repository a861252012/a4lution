<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class WfsStorageFee extends Model
{

    protected $guarded = [];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}