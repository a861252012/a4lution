<?php

namespace App\Policies;

use App\User;
use App\Item;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return ($user->isAdmin() || $user->isCreator());
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, Item $item)
    {
        if (env('IS_DEMO')){
            return ($user->isAdmin() || $user->isCreator()) && !in_array($item->id, [1]);
        }
        return ($user->isAdmin() || $user->isCreator());
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, Item $item)
    {
        if (env('IS_DEMO')){
            return ($user->isAdmin() || $user->isCreator()) && !in_array($item->id, [1, 2]);
        }
        return ($user->isAdmin() || $user->isCreator());
        
    }
}
