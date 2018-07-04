<?php

namespace App\Policies;

use App\User;
use App\ThingProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThingProfilePolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if ($user['is_admin']) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the thingProfile.
     *
     * @param  \App\User $user
     * @param  \App\ThingProfile $thingProfile
     * @return mixed
     */
    public function view(User $user, ThingProfile $thingProfile)
    {
        return $this->isOwner($user, $thingProfile);
    }

    /**
     * Determine whether the user can create thingProfiles.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user['active'];
    }

    /**
     * Determine whether the user can update the thingProfile.
     *
     * @param  \App\User $user
     * @param  \App\ThingProfile $thingProfile
     * @return mixed
     */
    public function update(User $user, ThingProfile $thingProfile)
    {
        return $this->isOwner($user, $thingProfile);
    }

    /**
     * Determine whether the user can delete the thingProfile.
     *
     * @param  \App\User $user
     * @param  \App\ThingProfile $thingProfile
     * @return mixed
     */
    public function delete(User $user, ThingProfile $thingProfile)
    {
        return $this->isOwner($user, $thingProfile);
    }

    /**
     * Determine whether the user is the owner of the thing profile.
     *
     * @param  \App\User $user
     * @param ThingProfile $thingProfile
     * @return mixed
     */
    private function isOwner(User $user, ThingProfile $thingProfile)
    {
        return $user['_id'] == $thingProfile['user_id'];
    }
}
