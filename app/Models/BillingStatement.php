<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BillingStatement extends Model
{
    protected $table = "billing_statements";

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($commissionSetting) {
            $commissionSetting->created_by = Auth::id();
            $commissionSetting->created_at = date('Y-m-d h:i:s');
            $commissionSetting->active = 1;
        });
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

    ###############
    ## Accessors ##
    ###############

    public function getCreatedAtTwAttribute(): Carbon
    {
        return $this->created_at->setTimezone((config('services.timezone.taipei')));
    }
}
