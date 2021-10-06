<?php

namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = "roles";

    protected $guarded = ['id'];

    public function roleassignment()
    {
        return $this->hasMany('App\Models\RoleAssignment', 'role_id', 'id');
    }
}
