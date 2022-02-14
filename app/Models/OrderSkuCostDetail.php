<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderSkuCostDetail extends Model
{
    protected $table = "order_sku_cost_details";

    protected $primaryKey = null;

    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('active', 0);
    }
}
