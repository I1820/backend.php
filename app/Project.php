<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class Project extends Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'things','updated_at','created_at','user_id'
    ];

    public function user()
    {
        return $this->belongsTo('User');
    }
}
