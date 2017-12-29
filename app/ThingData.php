<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ThingData extends Eloquent
{
    protected $connection = 'mongodb_data';
    protected $collection = 'things_data';
    const CREATED_AT = 'timestamp';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'thingid'
    ];


    public function thing()
    {
        return $this->belongsTo(Thing::class, 'thingid', 'mac_address');
    }
}
