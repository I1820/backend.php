<?php

namespace App\Policies;

use App\Codec;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CodecPolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if ($user['is_admin']) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the codec.
     *
     * @param User $user
     * @param Codec $codec
     * @return mixed
     */
    public function view(User $user, Codec $codec)
    {
        return $this->isOwner($user, $codec);
    }

    /**
     * Determine whether the user is the owner of the codec.
     *
     * @param User $user
     * @param Codec $codec
     * @return mixed
     */
    private function isOwner(User $user, Codec $codec)
    {
        return $user['_id'] == $codec['user_id'];
    }

    /**
     * Determine whether the user can create codecs.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'CREATE-CODEC')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can Send codec.
     *
     * @param User $user
     * @return mixed
     */
    public function send(User $user)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'SEND-CODEC')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can update the codec.
     *
     * @param User $user
     * @param Codec $codec
     * @return mixed
     */
    public function update(User $user, Codec $codec)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'UPDATE-CODEC')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can delete the codec.
     *
     * @param User $user
     * @param Codec $codec
     * @return mixed
     */
    public function delete(User $user, Codec $codec)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'DELETE-CODEC')->first();
        return $user['active'] && $permission;
    }
}
