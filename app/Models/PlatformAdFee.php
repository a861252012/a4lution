<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlatformAdFee extends Model
{
    public $timestamps = false;
    protected $table = "platform_ad_fees";
    protected $guarded = ['id'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
