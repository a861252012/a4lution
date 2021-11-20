<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformAdFee extends Model
{
    protected $table = "platform_ad_fees";

//    protected $fillable = ['client_code'];

    protected $guarded = ['id'];

    public $timestamps = false;

}
