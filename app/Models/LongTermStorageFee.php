<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LongTermStorageFee extends Model
{
    protected $table = "long_term_storage_fees";

    protected $guarded = ['id'];

    public $timestamps = false;

    public $incrementing = false;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
