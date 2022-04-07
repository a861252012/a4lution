<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContinStorageFee extends Model
{
    protected $table = "contin_storage_fees";

    protected $guarded = [];

    protected $casts = [
        'report_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($continStorage) {
            $continStorage->updated_by = Auth::id();
            $continStorage->created_by = Auth::id();
            $continStorage->active = 1;
        });

        static::updating(function ($continStorage) {
            $continStorage->updated_by = Auth::id();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}