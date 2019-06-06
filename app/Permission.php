<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Permission extends Eloquent
{
    protected $collection = 'permissions';
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
    ];

}
