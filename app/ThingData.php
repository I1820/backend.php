<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ThingData extends Eloquent
{
    const CREATED_AT = 'timestamp';
    protected $connection = 'mongodb_data';
    protected $collection = 'things_data';
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
