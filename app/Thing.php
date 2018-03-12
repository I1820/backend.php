<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed loc
 * @property mixed period
 * @property mixed description
 * @property mixed name
 */
class Thing extends Eloquent
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'loc', 'description', 'period', 'interface', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'created_at', 'user_id', 'id', 'project_id','profile_id', 'codec'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'item_id')
            ->where('item_type', 'thing')
            ->with('user');
    }

    public function profile()
    {
        return $this->belongsTo(ThingProfile::class);
    }
}
