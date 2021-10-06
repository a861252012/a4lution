<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSettings extends Model
{
    protected $table = "commission_settings";

    protected $primaryKey = null;

//    protected $fillable = ['client_code'];

    protected $guarded = [];

    public $incrementing = false;
}
