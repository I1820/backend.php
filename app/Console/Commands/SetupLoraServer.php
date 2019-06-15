<?php

namespace App\Console\Commands;

use App\Repository\Services\LoraService;
use Illuminate\Console\Command;

class SetupLoraServer extends Command
{

    /**
     * @var LoraService
     */
    protected $loraService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loraserver:setup {server=loraserver:8000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'setup loraserver.io service profile and network server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LoraService $loraService)
    {
        parent::__construct();
        $this->loraService = $loraService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $serverID = config('iot.lora.networkServerID');
        $this->info("network service id: " . $serverID);

        try {
            $info = $this->loraService->createNetworkServer($this->argument('server'));
            $this->info("network service id: " . $info->id);
            $serverID = $info->id;
        } catch (\App\Exceptions\LoraException $e) {
            $this->info("network service creation: " . $e->getMessage());
        }

        try {
            $info = $this->loraService->createServiceProfile($serverID);
            $this->info('service profile id: ' . $info->serviceProfileID);
        } catch (\App\Exceptions\LoraException $e) {
            $this->info("service profile creation: " . $e->getMessage());
        }
    }
}
