<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmazonReportList extends Model
{
    protected $table = "amazon_report_list";
    
    protected $primaryKey = null;

    protected $guarded = [];

    public $timestamps = false;

    public $incrementing = false;
}
