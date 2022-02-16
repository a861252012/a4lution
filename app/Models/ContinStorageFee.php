<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContinStorageFee extends Model
{
    protected $table = "contin_storage_fees";

    protected $guarded = [];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
