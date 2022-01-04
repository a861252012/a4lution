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

    ############
    ## Scopes ##
    ############

    public function getUpdatedAtTwAttribute()
    {
        return $this->updated_at->setTimezone('Asia/Taipei');
    }

    ###################
    ## Relationships ##
    ###################

    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('users.active', 1)
            ->where('customer_relations.active', 1)
            ->withTimestamps();
    }

    public function customerRelation()
    {
        return $this->hasMany(CustomerRelation::class, 'client_code', 'client_code')
            ->where('active', 1);
    }

    // 業務人員
    public function salesReps()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('customer_relations.role_id', 1)
            ->where('users.active', 1);
    }

    // 客服人員
    public function accountServices()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('customer_relations.role_id', 4)
            ->where('users.active', 1);
    }

    public function commission()
    {
        return $this->hasOne(CommissionSetting::class, 'client_code', 'client_code')
            ->where('active', 1);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

