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
}
