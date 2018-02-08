<?php

return [
    'lora' => [
        'serverBaseUrl' => env('LORA_BASE_URL','https://platform.ceit.aut.ac.ir:50013'),
        'applicationID' => "1",
        'organizationID' => '1',
        'networkServerID' => '1',
    ],
    'core' => [
        'serverBaseUrl' => env('CORE_BASE_URL','172.23.132.50'),
        'port' => '8080'
    ],

];
