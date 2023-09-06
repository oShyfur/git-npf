<?php

return [
    'base_url' => env('DOPTOR_BASE_URL', ''),
    'live' => [
        'app_id' => env('DOPTOR_lIVE_APP_ID', ''),
        'app_secret' => env('DOPTOR_lIVE_APP_SECRET', ''),
        'idp_url' => env('DOPTOR_lIVE_IDP_URL', ''),
        'ip_url' => env('DOPTOR_lIVE_IP_URL', ''),
    ],
    'training' => [
        'app_id' => env('DOPTOR_TRAINING_APP_ID', ''),
        'app_secret' => env('DOPTOR_TRAINING_APP_SECRET', ''),
        'idp_url' => env('DOPTOR_TRAINING_IDP_URL', ''),
        'ip_url' => env('DOPTOR_TRAINING_IP_URL', ''),
    ],
    'token' => env('DOPTOR_TOKEN','')
];
