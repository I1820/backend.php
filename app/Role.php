<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Role extends Eloquent
{
    protected $collection = 'roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function perms()
    {
        $permission = DB::collection('permissions')->whereIn('_id', $this['permissions'])->get();
        if (!$permission)
            return collect([]);
        $permission = $permission->map(function ($item) {
            $item['_id'] = (string)($item['_id']);
            return $item;
        });
        return $permission;
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

}
