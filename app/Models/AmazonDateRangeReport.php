<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmazonDateRangeReport extends Model
{
    protected $table = "amazon_date_range_report";

    protected $guarded = ['id'];

    public $timestamps = false;

    public $incrementing = false;
}
