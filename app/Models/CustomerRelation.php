<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRelation extends Model
{
    protected $table = "customer_relations";

    protected $primaryKey = null;

//    public $timestamps = false;

    public $incrementing = false;

    public function users()
    {
        return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }
}

