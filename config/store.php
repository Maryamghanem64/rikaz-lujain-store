<?php

return [
    'admins' => [
        'rikaz' => [
            'name' => env('RIKAZ_ADMIN_NAME', 'Rikaz Admin'),
            'email' => env('RIKAZ_ADMIN_EMAIL'),
            'password' => env('RIKAZ_ADMIN_PASSWORD'),
        ],
        'lujain' => [
            'name' => env('LUJAIN_ADMIN_NAME', 'Lujain Admin'),
            'email' => env('LUJAIN_ADMIN_EMAIL'),
            'password' => env('LUJAIN_ADMIN_PASSWORD'),
        ],
    ],
];
