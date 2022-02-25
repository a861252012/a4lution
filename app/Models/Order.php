<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $table = "orders";

    protected $primaryKey = 'order_code';

    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;

    protected $keyType = 'string';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('active', 0);
    }
}
