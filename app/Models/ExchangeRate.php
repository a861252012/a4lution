<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExchangeRate extends Model
{
    protected $table = "exchange_rates";

    protected $primaryKey = null;

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;

    ###########
    ## SCOPE ##
    ###########
    public function scopeActive($q)
    {
        return $q->where('active', 1);
    }

    public function scopeInActive($q)
    {
        return $q->where('active', 0);
    }

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
