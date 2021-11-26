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
    public function scopeActive($q)
    {
        return $q->where('active', 1);
    }

    public function scopeInActive($q)
    {
        return $q->where('active', 0);
    }
}
