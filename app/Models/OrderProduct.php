<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderProduct extends Model
{
    protected $table = "order_products";

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($exchangeRate) {
            $exchangeRate->updated_by = Auth::id();
            $exchangeRate->created_by = Auth::id();
            $exchangeRate->active = 1;
        });
        static::updating(function ($exchangeRate) {
            $exchangeRate->updated_by = Auth::id();
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
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
}
