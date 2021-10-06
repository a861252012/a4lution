<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = "invoices";

//    protected $fillable = ['client_code'];

    protected $guarded = ['id'];

    public function getCreatedAtAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['created_at'])->setTimezone(env('TIME_ZONE_A'));
    }
}
