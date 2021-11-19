<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExtraordinaryItem extends Model
{
    protected $table = "extraordinary_items";

    protected $guarded = ['id'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($extraordinaryItem) {
            $extraordinaryItem->updated_by = Auth::id();
            $extraordinaryItem->created_by = Auth::id();
            $extraordinaryItem->active = 1;
        });

        static::updating(function ($extraordinaryItem) {
            $extraordinaryItem->updated_by = Auth::id();
        });

        static::addGlobalScope('isActive', function (Builder $builder) {
            $builder->where('active', 1);
        });
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', 0);
    }
}
