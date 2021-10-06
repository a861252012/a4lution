<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRates extends Model
{
    protected $table = "exchange_rates";

    protected $primaryKey = null;

//    protected $fillable = ['client_code'];

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;
}
