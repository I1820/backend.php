<?php

namespace App;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * @property array $loc
 * @property integer $period
 * @property string $description
 * @property string $name
 * @property array $interface
 * @property string $dev_eui
 * @property string $model
 * @property string $type
 * @property boolean $active
 * @property string $activation
 */
class Thing extends Eloquent
{

    protected $appends = ['keys', 'owner'];
    protected $lora_thing;
    protected $last_parsed;
    protected $lora_activation;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'loc', 'description', 'period', 'interface', 'type', 'dev_eui', 'active', 'activation', 'keys', 'model'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'created_at', 'user_id', 'id', 'project_id', 'profile_id', 'codec', 'permissions'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function profile()
    {
        return $this->belongsTo(ThingProfile::class);
    }

    public function getCodecAttribute($value)
    {
        if (!$value)
            return "";
        return $value;
    }

    public function getOwnerAttribute($value)
    {
        $user = $this->user()->first();
        if ($user)
            return $user;
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getKeysAttribute()
    {
        if ($this->activation == 'ABP' || $this->activation == 'JWT')
            return isset($this->attributes['keys']) ? $this->attributes['keys'] : [];
        try {
            if (!$this->lora_activation) {
                $loraService = resolve('App\Repository\Services\LoraService');
                $this->lora_activation = $loraService->getActivation($this->dev_eui);
            }
            return array_merge(json_decode(json_encode($this->lora_activation, true)), $this->attributes['keys']);
        } catch (Exception $e) {
            return isset($this->attributes['keys']) ? $this->attributes['keys'] : [];
        }

    }
}
