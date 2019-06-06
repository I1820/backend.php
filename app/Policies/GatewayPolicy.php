<?php

namespace App\Policies;

use App\Gateway;
use App\User;
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
     * @param User $user
     * @param Gateway $gateway
     * @return mixed
     */
    public function view(User $user, Gateway $gateway)
    {
        return true;
    }

    /**
     * Determine whether the user can create gateways.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'CREATE-GATEWAY')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can update the gateway.
     *
     * @param User $user
     * @param Gateway $gateway
     * @return mixed
     */
    public function update(User $user, Gateway $gateway)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'UPDATE-GATEWAY')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can delete the gateway.
     *
     * @param User $user
     * @param Gateway $gateway
     * @return mixed
     */
    public function delete(User $user, Gateway $gateway)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'DELETE-GATEWAY')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user is the owner of the gateway.
     *
     * @param User $user
     * @param Gateway $gateway
     * @return mixed
     */
    private function isOwner(User $user, Gateway $gateway)
    {
        return $user['_id'] == $gateway['user_id'];
    }
}
