<?php

return [
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'web' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
];
