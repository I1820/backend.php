<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Token extends Eloquent
{
    public $timestamps = false;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
