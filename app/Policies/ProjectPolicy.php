<?php

namespace App\Policies;

use App\Permission;
use App\Repository\Services\PermissionService;
use App\User;
use App\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function before($user)
    {
        if ($user['is_admin']) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  \App\User $user
     * @param  \App\Project $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return $this->isOwner($user, $project);
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {

        $permission = $user->role()->first()->perms()->where('slug', 'CREATE-PROJECT')->first();
        return $user['active'] && $permission;
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  \App\User $user
     * @param  \App\Project $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'EDIT-PROJECT')->first();
        return $this->isOwner($user, $project) && $permission;
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  \App\User $user
     * @param  \App\Project $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        $permission = $user->role()->first()->perms()->where('slug', 'DELETE-PROJECT')->first();
        return $this->isOwner($user, $project) && $permission;
    }

    /**
     * Determine whether the user is the owner of the project.
     *
     * @param  \App\User $user
     * @param  \App\Project $project
     * @return mixed
     */
    private function isOwner(User $user, Project $project)
    {
        return $project['owner']['id'] == $user['id'];
    }
}
