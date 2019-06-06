<?php

namespace App\Policies;

use App\Package;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackagePolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if ($user['is_admin']) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the package.
     *
     * @param User $user
     * @param Package $package
     * @return mixed
     */
    public function view(User $user, Package $package)
    {
        return true;
    }

    /**
     * Determine whether the user can create packages.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the package.
     *
     * @param User $user
     * @param Package $package
     * @return mixed
     */
    public function update(User $user, Package $package)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the package.
     *
     * @param User $user
     * @param Package $package
     * @return mixed
     */
    public function delete(User $user, Package $package)
    {
        return $user->isAdmin();
    }
}
