<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";

    protected $primaryKey = 'order_code';

    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;

    protected $keyType = 'string';
}
