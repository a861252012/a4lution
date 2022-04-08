<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    protected $table = "invoices";

    protected $guarded = ['id'];

    protected $casts = [
        'report_date' => 'date',
        'issue_date' => 'date',
        'due_date' => 'date'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            $invoice->updated_by = Auth::id();
            $invoice->created_by = Auth::id();
            $invoice->active = 1;
        });

        static::updating(function ($invoice) {
            $invoice->updated_by = Auth::id();
        });
    }

    public function getCreatedAtAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['created_at'])->setTimezone((config('services.timezone.taipei')));
    }

    ################
    ## Relations ##
    ################
    public function billingStatement(): BelongsTo
    {
        return $this->belongsTo(BillingStatement::class);
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
