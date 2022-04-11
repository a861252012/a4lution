<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ReturnHelperCharge extends Model
{
    protected $guarded = [];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('return_helper_charges.active', 1);
    }
}