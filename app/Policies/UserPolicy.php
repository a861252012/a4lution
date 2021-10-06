<?php

namespace App\Policies;

use App\Models\Users;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the users.
     *
     * @param Users $user
     * @return boolean
     */
    public function viewAny(Users $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can create users.
     *
     * @param Users $user
     * @return boolean
     */
    public function create(Users $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can update the user.
     *
     * @param Users $user
     * @param Users $model
     * @return boolean
     */
    public function update(Users $user, Users $model)
    {
        if (env('IS_DEMO')){
            return $user->isAdmin() && !in_array($model->id, [1, 2, 3]);
        }
        return $user->isAdmin() || $model->id == $user->id;
    }

    /**
     * Determine whether the authenticate user can delete the user.
     *
     * @param Users $user
     * @param Users $model
     * @return boolean
     */
    public function delete(Users $user, Users $model) {
        if (env('IS_DEMO')){
            return $user->isAdmin() && $user->id != $model->id && !in_array($model->id, [1, 2, 3]);
        }
        return $user->isAdmin() && $user->id != $model->id;
    }

    /**
     * Determine whether the authenticate user can manage other users.
     *
     * @param Users $user
     * @return boolean
     */
    public function manageUsers(Users $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can manage items and other related entities(tags, categories).
     *
     * @param Users $user
     * @return boolean
     */
    public function manageItems(Users $user)
    {
        return $user->isAdmin() || $user->isCreator();
    }
}
