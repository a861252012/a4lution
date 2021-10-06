<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingStatements extends Model
{
    protected $table = "billing_statements";

//    protected $fillable = ['client_code'];

    protected $guarded = ['id'];

    public $timestamps = false;

//    public $incrementing = false;
}
