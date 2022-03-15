<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FirstMileShipmentFee extends Model
{
    protected $table = "first_mile_shipment_fees";

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

}
