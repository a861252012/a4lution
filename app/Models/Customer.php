<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = "customers";

    protected $primaryKey = null;

    public $incrementing = false;

    protected $casts = [
        'contract_date' => 'date',
    ];

    ###################
    ## Relationships ##
    ###################

    public function salesReps()
    {
        return $this->belongsToMany(User::class, 'customer_relations', 'client_code', 'user_id', 'client_code')
            ->where('users.active', 1);
    }

    public function commission()
    {
        return $this->hasOne(CommissionSetting::class, 'client_code', 'client_code')
            ->where('active', 1);
    }

}

