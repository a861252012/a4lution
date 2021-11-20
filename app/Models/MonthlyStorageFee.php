<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyStorageFee extends Model
{
    protected $table = "monthly_storage_fees";

    protected $guarded = ['id'];

    public $timestamps = false;
}

