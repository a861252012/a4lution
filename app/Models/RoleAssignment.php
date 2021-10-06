<?php

namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleAssignment extends Model
{
    use HasFactory;

    protected $table = "role_assignment";

    protected $fillable = ['user_id', 'role_id', 'active'];

    /**
     * Get the users for the role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsTo('App\Models\Roles', 'role_id', 'id');
    }
}
