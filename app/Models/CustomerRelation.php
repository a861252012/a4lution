<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRelation extends Model
{
    protected $table = "customer_relations";

    protected $primaryKey = null;

    public $incrementing = false;

    ###################
    ## Relationships ##
    ###################

    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}

