<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CommissionSkuSetting extends Model
{
    protected $table = "commission_sku_settings";

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($customer) {
            $customer->updated_by = Auth::id();
            $customer->created_by = Auth::id();
            $customer->active = 1;
        });

        static::updating(function ($customer) {
            $customer->updated_by = Auth::id();
        });
    }

    ############
    ## Scopes ##
    ############
    public function scopeActive($q)
    {
        return $q->where('active', '1');
    }

    ###############
    ## Accessors ##
    ###############

    public function getUpdatedAtTwAttribute()
    {
        return $this->updated_at->setTimezone('Asia/Taipei');
    }

    ###################
    ## Relationships ##
    ###################

    public function creator()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}