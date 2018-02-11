<?php

namespace App;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Eloquent implements AuthenticatableContract, AuthorizableContract
{
    use Authorizable;
    use Authenticatable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'other_info', 'active', 'legal', 'mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'active', '_id', 'updated_at', 'created_at', 'files'
    ];

    public function projects()
    {
        return $this->permissions()->where('item_type', 'project')->with('project', 'project.things');
    }

    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }

    public function things()
    {
        return $this->permissions()->where('item_type', 'thing')->with(['thing', 'thing.project']);
    }


    public function permissions()
    {
        return $this->hasMany(Permission::class, 'user_id');
    }

    public function gateways()
    {
        return $this->hasMany(Gateway::class,'user_id');
    }

    public function codecs()
    {
        return $this->hasMany(Codec::class, 'user_id');
    }
}
