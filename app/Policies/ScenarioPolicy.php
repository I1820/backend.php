<?php

namespace App\Policies;

use App\User;
use App\Scenario;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScenarioPolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if ($user['is_admin']) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the scenario.
     *
     * @param  \App\User  $user
     * @param  \App\Scenario  $scenario
     * @return mixed
     */
    public function view(User $user, Scenario $scenario)
    {
        return $this->isOwner($user,$scenario);
    }

    /**
     * Determine whether the user can create scenarios.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'CREATE-SCENARIO')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can update the scenario.
     *
     * @param  \App\User  $user
     * @param  \App\Scenario  $scenario
     * @return mixed
     */
    public function update(User $user, Scenario $scenario)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'UPDATE-SCENARIO')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can delete the scenario.
     *
     * @param  \App\User  $user
     * @param  \App\Scenario  $scenario
     * @return mixed
     */
    public function delete(User $user, Scenario $scenario)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'DELETE-SCENARIO')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user is the owner of the scenario.
     *
     * @param  \App\User $user
     * @param Scenario $scenario
     * @return mixed
     */
    private function isOwner(User $user, Scenario $scenario){
        return $scenario['user_id'] == $user['id'];
    }
}
