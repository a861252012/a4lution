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

    ############
    ## Scopes ##
    ############

    public function getBasicRatePercentageAttribute()
    {
        if (!$this->basic_rate) {
            return '';
        }

        return $this->basic_rate * 100;
    }

    public function getTier1RatePercentageAttribute()
    {
        if (!$this->tier_1_rate) {
            return '';
        }

        return $this->tier_1_rate * 100;
    }

    public function getTier2RatePercentageAttribute()
    {
        if (!$this->tier_2_rate) {
            return '';
        }

        return $this->tier_2_rate * 100;
    }

    public function getTier3RatePercentageAttribute()
    {
        if (!$this->tier_3_rate) {
            return '';
        }

        return $this->tier_3_rate * 100;
    }

    public function getTier4RatePercentageAttribute()
    {
        if (!$this->tier_4_rate) {
            return '';
        }

        return $this->tier_4_rate * 100;
    }

    public function getTierTopRatePercentageAttribute()
    {
        if (!$this->tier_top_rate) {
            return '';
        }

        return $this->tier_top_rate * 100;
    }

    public function getPercentageOffPromotionAttribute()
    {
        if (!$this->promotion_threshold) {
            return '';
        }

        return (1 - $this->promotion_threshold) * 100;
    }

    public function getTierPromotionPercentageAttribute()
    {
        if (!$this->tier_promotion) {
            return '';
        }

        return $this->tier_promotion * 100;
    }
}
