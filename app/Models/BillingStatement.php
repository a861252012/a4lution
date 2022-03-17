<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BillingStatement extends Model
{
    protected $table = "billing_statements";

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'opex_invoice' => 'float',
        "fba_storage_fee_invoice" => 'float',
        "sales_credit" => 'float',
        "a4_account_refund_and_resend" => 'float',
        "a4_account_sales_amount" => 'float',
        "a4_account_platform_fee" => 'float',
        "a4_account_fba_fee" => 'float',
        "a4_account_fba_storage_fee" => 'float',
        "sales_tax_handling" => 'float',
        "a4_account_miscellaneous" => 'float',
        "extraordinary_item" => 'float',
        "avolution_commission" => 'float',
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
}
