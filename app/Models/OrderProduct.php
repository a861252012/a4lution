<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderProduct extends Model
{
    protected $table = "order_products";

    protected $guarded = [];

    protected $casts = [
        'sales_amount' => 'float',
        "paypal_fee" => 'float',
        "transaction_fee" => 'float',
        "fba_fee" => 'float',
        "first_mile_shipping_fee" => 'float',
        "first_mile_tariff" => 'float',
        "last_mile_shipping_fee" => 'float',
        "other_fee" => 'float',
        "purchase_shipping_fee" => 'float',
        "product_cost" => 'float',
        "marketplace_tax" => 'float',
        "cost_of_point" => 'float',
        "exclusives_referral_fee" => 'float',
        "other_transaction" => 'float'
    ];

    protected static function booted()
    {
        static::creating(function ($orderProduct) {
            $orderProduct->updated_by = Auth::id();
            $orderProduct->created_by = Auth::id();
            $orderProduct->active = 1;
        });
        static::updating(function ($orderProduct) {
            $orderProduct->updated_by = Auth::id();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('active', 0);
    }
}
