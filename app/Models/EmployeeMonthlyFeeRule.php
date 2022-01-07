<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class EmployeeMonthlyFeeRule extends Model
{
    protected $table = "employee_monthly_fee_rules";

    protected $guarded = ['id'];

    protected $casts = [
        'rate_base' => 'float',
        'rate' => 'float',
        'tier_1_first_year' => 'float',
        'tier_2_first_year' => 'float',
        'tier_1_over_a_year' => 'float',
        'tier_2_over_a_year' => 'float',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($exchangeRate) {
            $exchangeRate->updated_by = Auth::id();
            $exchangeRate->created_by = Auth::id();
            $exchangeRate->active = 1;
        });
        static::updating(function ($exchangeRate) {
            $exchangeRate->updated_by = Auth::id();
        });
    }

    ###################
    ## Relationships ##
    ###################

    public function roles(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
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
