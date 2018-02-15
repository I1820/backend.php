<?php

return [
    'lora' => [
        'serverBaseUrl' => env('LORA_BASE_URL','https://platform.ceit.aut.ac.ir:50013'),
        'organizationID' => '1',
        'networkServerID' => '1',
        'serviceProfileID' => '2dc02c2b-2be9-4bef-ade4-f3408e62a08c',
    ],
    'core' => [
        'serverBaseUrl' => env('CORE_BASE_URL','172.23.132.50'),
        'port' => '8080',
        'dmPort' => '1372',
        'gmPort' => '1373'
    ],

];
