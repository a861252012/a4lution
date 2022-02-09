<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Statement extends Model
{
    use HasFactory;

    protected $table = "statements";

    protected $guarded = ['id'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($statement) {
            $statement->updated_by = Auth::id();
            $statement->created_by = Auth::id();
            $statement->transaction_type = 'deposit';
            $statement->amount_type = 'monthly_fee';
            $statement->is_dirty = 1;
            $statement->active = 1;
        });

        static::updating(function ($invoice) {
            $invoice->updated_by = Auth::id();
        });
    }

    ###############
    ## Accessors ##
    ###############

    public function getFormattedBillingMonthAttribute(): string
    {
        return Carbon::createFromFormat('Ym', $this->billing_month)
            ->setTimezone(config('services.timezone.taipei'))
            ->format('M-Y');
    }
}
