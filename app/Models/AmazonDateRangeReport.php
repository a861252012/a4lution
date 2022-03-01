<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AmazonDateRangeReport extends Model
{
    protected $table = "amazon_date_range_report";

    protected $guarded = ['id'];

    public $timestamps = false;

    public $incrementing = false;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
