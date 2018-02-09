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
        'name', 'description', 'active', 'container'
    ];
    protected $appends = ['owner'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'created_at', 'user_id', 'permissions'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'item_id')
            ->where('item_type', 'project')
            ->with('user');
    }


    public function things()
    {
        return $this->hasMany(Thing::class)->with('user');
    }



    public function getOwnerAttribute($value)
    {
        foreach ($this->permissions as $permissions)
            if ($permissions['name'] == 'PROJECT-OWNER')
                return $permissions['user'];
        return null;
    }

}
