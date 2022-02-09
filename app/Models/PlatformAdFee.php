<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PlatformAdFee extends Model
{
    protected $table = "platform_ad_fees";

    public $timestamps = false;

    protected $guarded = ['id'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
