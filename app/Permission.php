<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;


class Permission extends Eloquent
{
    protected $collection = 'users_permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'item_type', 'permission_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'created_at', 'user_id', '_id', 'item_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
