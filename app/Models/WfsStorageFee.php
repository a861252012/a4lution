<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WfsStorageFee extends Model
{
    protected $guarded = [];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    ###############
    ## Accessors ##
    ###############

    public function getCreatedAtTwAttribute(): Carbon
    {
        return $this->created_at->setTimezone((config('services.timezone.taipei')));
    }
}
