<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContinStorageFee extends Model
{
    protected $table = "contin_storage_fees";

    protected $guarded = ['id'];
    
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
