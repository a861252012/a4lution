<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSkuCostDetail extends Model
{
    protected $table = "order_sku_cost_details";

    protected $primaryKey = null;

    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;
}
