<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the users.
     *
     * @param User $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can create users.
     *
     * @param User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can update the user.
     *
     * @param User $user
     * @param User $model
     * @return boolean
     */
    public function update(User $user, User $model)
    {
        if (env('IS_DEMO')){
            return $user->isAdmin() && !in_array($model->id, [1, 2, 3]);
        }
        return $user->isAdmin() || $model->id == $user->id;
    }

    /**
     * Determine whether the authenticate user can delete the user.
     *
     * @param User $user
     * @param User $model
     * @return boolean
     */
    public function delete(User $user, User $model) {
        if (env('IS_DEMO')){
            return $user->isAdmin() && $user->id != $model->id && !in_array($model->id, [1, 2, 3]);
        }
        return $user->isAdmin() && $user->id != $model->id;
    }

    /**
     * Determine whether the authenticate user can manage other users.
     *
     * @param User $user
     * @return boolean
     */
    public function manageUsers(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can manage items and other related entities(tags, categories).
     *
     * @param User $user
     * @return boolean
     */
    public function manageItems(User $user)
    {
        return $user->isAdmin() || $user->isCreator();
    }
}
