<?php

namespace App\Providers;

use App\Codec;
use App\Gateway;
use App\Package;
use App\Policies\CodecPolicy;
use App\Policies\GatewayPolicy;
use App\Policies\PackagePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ScenarioPolicy;
use App\Policies\ThingPolicy;
use App\Policies\ThingProfilePolicy;
use App\Project;
use App\Scenario;
use App\Thing;
use App\ThingProfile;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Thing::class => ThingPolicy::class,
        ThingProfile::class => ThingProfilePolicy::class,
        Gateway::class => GatewayPolicy::class,
        Scenario::class => ScenarioPolicy::class,
        Codec::class => CodecPolicy::class,
        Package::class => PackagePolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
