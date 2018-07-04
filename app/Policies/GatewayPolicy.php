<?php

namespace App\Policies;

use App\User;
use App\Gateway;
use Illuminate\Auth\Access\HandlesAuthorization;

class GatewayPolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if ($user['is_admin']) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the gateway.
     *
     * @param  \App\User $user
     * @param  \App\Gateway $gateway
     * @return mixed
     */
    public function view(User $user, Gateway $gateway)
    {
        return $this->isOwner($user,$gateway);
    }

    /**
     * Determine whether the user can create gateways.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user['active'];
    }

    /**
     * Determine whether the user can update the gateway.
     *
     * @param  \App\User $user
     * @param  \App\Gateway $gateway
     * @return mixed
     */
    public function update(User $user, Gateway $gateway)
    {
        return $this->isOwner($user,$gateway);
    }

    /**
     * Determine whether the user can delete the gateway.
     *
     * @param  \App\User $user
     * @param  \App\Gateway $gateway
     * @return mixed
     */
    public function delete(User $user, Gateway $gateway)
    {
        return $this->isOwner($user, $gateway);
    }

    /**
     * Determine whether the user is the owner of the gateway.
     *
     * @param  \App\User $user
     * @param Gateway $gateway
     * @return mixed
     */
    private function isOwner(User $user, Gateway $gateway)
    {
        return $user['_id'] == $gateway['user_id'];
    }
}
