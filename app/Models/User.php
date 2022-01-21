<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class User extends Authenticatable
{
    use Notifiable, HasRelationships;

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

    ###################
    ## Relationships ##
    ###################

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_assignment');
    }

    public function roleAssignment(): HasOne
    {
        return $this->hasOne('App\Models\RoleAssignment', 'user_id', 'id');
    }

    public function batchJobs(): HasMany
    {
        return $this->hasMany('App\Models\BatchJob', 'user_id', 'id');
    }

    public function customerRelations(): HasMany
    {
        return $this->hasMany('App\Models\CustomerRelation', 'user_id', 'id');
    }

    public function mainViews()
    {
        return $this->hasManyDeep(View::class, [
            'role_assignment', // pivot table: user <-> role
            Role::class, // many-to-many model
            'view_permission' // pivot table: role <-> view
        ])
            ->has('subViews')
            ->where('level', 1)
            ->where('views.active', 1)
            ->orderByRaw('views.module , views.level , views.order');
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
