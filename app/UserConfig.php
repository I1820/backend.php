<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserConfig extends Eloquent
{
    protected $appends = [''];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'widget',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        '_id'
    ];
}
