<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = "customers";

    protected $primaryKey = 'client_code';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'contract_date' => 'date',
    ];

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

    ###################
    ## Relationships ##
    ###################

    public function salesReps()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('users.active', 1);
    }

    public function commission()
    {
        return $this->hasOne(CommissionSetting::class, 'client_code', 'client_code');
    }

    ############
    ## Others ##
    ############
    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    public function isInActive(): bool
    {
        return ! (bool) $this->active;
    }

}

