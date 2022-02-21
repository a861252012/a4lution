<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = "customers";

    protected $primaryKey = 'client_code';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'contract_period_start' => 'date',
        'contract_period_end' => 'date',
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
            ->where('customer_relations.active', 1)
            ->where('customer_relations.role_id', 1)
            ->where('users.active', 1);
    }

    // 客服人員
    public function accountServices()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('customer_relations.active', 1)
            ->where('customer_relations.role_id', 4)
            ->where('users.active', 1);
    }

    // OP人員
    public function operationUsers()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('customer_relations.active', 1)
            ->where('customer_relations.role_id', 3)
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

