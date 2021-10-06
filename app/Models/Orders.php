<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = "orders";

    protected $primaryKey = 'order_code';

    public $incrementing = false;

    protected $keyType = 'string';
}
