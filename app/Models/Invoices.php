<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = "invoices";

    protected $guarded = ['id'];

    public function getCreatedAtAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['created_at'])->setTimezone(env('TIME_ZONE_A'));
    }

    /**
     * Scope a query to only include active users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
