<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed name
 * @property mixed description
 */
class Project extends Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'active'
    ];
    protected $appends = ['owner'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'created_at', 'user_id', 'roles'
    ];

    public function roles()
    {
        return $this->hasMany(Role::class)->with('user');
    }

    public function things()
    {
        return $this->hasMany(Thing::class)->with('user');
    }

    public function getOwnerAttribute($value)
    {
        foreach ($this->roles as $role)
            if (isset($role['permissions']['owner']))
                return $role['user'];
        return null;
    }

}
