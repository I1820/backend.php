<?php

namespace App\Policies;

use App\User;
use App\Thing;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the thing.
     *
     * @param  \App\User $user
     * @param  \App\Thing $thing
     * @return mixed
     */
    public function view(User $user, Thing $thing)
    {
        return $this->isOwner($user,$thing);
    }

    /**
     * Determine whether the user can create things.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user['active'];
    }

    /**
     * Determine whether the user can update the thing.
     *
     * @param  \App\User $user
     * @param  \App\Thing $thing
     * @return mixed
     */
    public function update(User $user, Thing $thing)
    {
        return $this->isOwner($user,$thing);
    }

    /**
     * Determine whether the user can delete the thing.
     *
     * @param  \App\User $user
     * @param  \App\Thing $thing
     * @return mixed
     */
    public function delete(User $user, Thing $thing)
    {
        return $this->isOwner($user,$thing);
    }


    /**
     * Determine whether the user is the owner of the thing.
     *
     * @param  \App\User $user
     * @param Thing $thing
     * @return mixed
     */
    private function isOwner(User $user, Thing $thing)
    {
        return $thing['user_id'] == $user['id'];
    }
}
