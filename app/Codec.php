<?php

namespace App;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class Codec extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code','user_id', 'project_id'
    ];
    protected $appends = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'created_at', 'project_id', 'user_id'
    ];

    public function thing()
    {
        return $this->belongsTo(Thing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
