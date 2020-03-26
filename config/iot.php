<?php

return [
    'lora' => [
        'serverBaseUrl' => env(
            'LORA_BASE_URL',
            'https://platform.ceit.aut.ac.ir:50013'
        ),
        'organizationID' => env('LORA_ORG_ID', '1'),
        'networkServerID' => env('LORA_NET_SERVER_ID', '1'),
        'applicationID' => env('LORA_APP_ID', '1'),
        'serviceProfileID' => env(
            'LORA_SERVICE_PROFILE_ID',
            '2dc02c2b-2be9-4bef-ade4-f3408e62a08c'
        ),
    ],
    'core' => [
        'gm' => [
            'url'  => env('CORE_GM_URL', '127.0.0.1:1995')
        ],
        'tm' => [
            'url'=> env('CORE_TM_URL', '127.0.0.1:1378')
        ],
        'pm' => [
            'url'=> env('CORE_PM_URL', '127.0.0.1:1999')
        ],
        'dm' => [
            'url'=> env('CORE_DM_URL', '127.0.0.1:1373')
        ],
    ],
    'lan' => [
        'serverBaseUrl' => env('LAN_BASE_URL', '172.23.132.50:9000'),
    ],
];
