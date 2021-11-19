<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function scopeActive($q, $active = 1)
    {
        return $q->where('active', $active);
    }

    public function scopeInActive($q)
    {
        return $q->scopeActive('active', 0);
    }
}
