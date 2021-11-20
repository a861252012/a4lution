<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "roles";

    protected $guarded = ['id'];

    public function roleAssignment()
    {
        return $this->hasMany('App\Models\RoleAssignment', 'role_id', 'id');
    }
}
