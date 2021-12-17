<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $table = "commission_settings";

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($commissionSetting) {
            $commissionSetting->updated_by = Auth::id();
            $commissionSetting->created_by = Auth::id();
            $commissionSetting->active = 1;
        });

        static::updating(function ($commissionSetting) {
            $commissionSetting->updated_by = Auth::id();
        });
    }
}
