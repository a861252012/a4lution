<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirstMileShipmentFees extends Model
{
    protected $table = "first_mile_shipment_fees";

//    protected $fillable = ['client_code'];

    protected $guarded = ['id'];

    public $timestamps = false;

    public $incrementing = false;

}
