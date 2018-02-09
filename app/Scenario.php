<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Eloquent
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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function active(){
        $other_scenarios = $this->project()->first()->scenarios();
        foreach ($other_scenarios as $scenario){
            if($scenario['active'] == true){
                $scenario['active'] = false;
                $scenario->save();
            }
        }
        $this['active'] = true;
    }
}
