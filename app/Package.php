<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class Package extends Eloquent
{


    protected $appends = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'node_num', 'project_num', 'time', 'price'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'is_active'
    ];

    public function things()
    {
        return $this->hasMany(Thing::class)->with('user');
    }

    public function getNameAttribute($value)
    {
        return $this['data']['name'];
    }
}
