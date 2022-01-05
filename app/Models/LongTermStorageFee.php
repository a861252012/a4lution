<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LongTermStorageFee extends Model
{
    protected $table = "long_term_storage_fees";

    protected $guarded = ['id'];

    public $timestamps = false;

    public $incrementing = false;
}
