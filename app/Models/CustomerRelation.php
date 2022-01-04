<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRelation extends Model
{
    protected $table = "customer_relations";

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($customerRelation) {
            $customerRelation->updated_by = Auth::id();
            $customerRelation->created_by = Auth::id();
            $customerRelation->active = 1;
        });

        static::updating(function ($customerRelation) {
            $customerRelation->updated_by = Auth::id();
        });
    }

    ###################
    ## Relationships ##
    ###################

    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}

