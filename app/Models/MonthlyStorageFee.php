<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MonthlyStorageFee extends Model
{
    protected $table = "monthly_storage_fees";

    protected $guarded = ['id'];

    public $timestamps = false;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}

