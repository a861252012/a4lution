<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\BillingStatements;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = "invoices";

//    protected $fillable = ['client_code'];

    protected $guarded = ['id'];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function getCreatedAtAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['created_at'])->setTimezone(env('TIME_ZONE_A'));
    }

    ################
    ## Relations ##
    ################
    public function billingStatement()
    {
        return $this->belongsTo(BillingStatements::class);
    }
}
