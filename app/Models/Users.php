<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = "users";

    protected $primaryKey = "id";

    protected $hidden = ['password'];

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'actor_type',
        'full_name',
        'company_name',
        'phone_number',
        'address',
        'active',
        'created_by',
        'updated_by'
    ];

    /**
     * Get the role of the user
     *
     * @return HasOne
     */
    public function roleAssignment(): HasOne
    {
        return $this->hasOne('App\Models\RoleAssignment', 'user_id', 'id');
    }

    public function batchJobs(): HasMany
    {
        return $this->hasMany('App\Models\BatchJobs', 'user_id', 'id');
    }

    public function customerRelations(): HasMany
    {
        return $this->hasMany('App\Models\CustomerRelations', 'user_id', 'id');
    }

    /**
     * Get the path to the profile picture
     *
     * @return string
     */
    public function profilePicture(): string
    {
        if ($this->picture) {
            return "/{$this->picture}";
        }

        return asset('pictures') . '/people_icon.jpg';
    }


    /**
     * Check if the user has admin role
     *
     * @return boolean
     */
    public function isManager(): bool
    {
        return $this->roleAssignment->role_id === 2;
    }
//    /**
//     * Check if the user has admin role
//     *
//     * @return boolean
//     */
//    public function isAdmin(): bool
//    {
//        return $this->role_id == 1;
//    }
//
//    /**
//     * Check if the user has creator role
//     *
//     * @return boolean
//     */
//    public function isCreator(): bool
//    {
//        return $this->role_id == 2;
//    }
//
//    /**
//     * Check if the user has user role
//     *
//     * @return boolean
//     */
//    public function isMember(): bool
//    {
//        return $this->role_id == 3;
//    }
}
