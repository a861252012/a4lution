<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderBulkUpdate extends Model
{
    protected $table = "order_bulk_updates";

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($orderBulkUpdate) {
            $orderBulkUpdate->created_at = date('Y-m-d h:i:s');
            $orderBulkUpdate->created_by = Auth::id();
        });
    }
}
