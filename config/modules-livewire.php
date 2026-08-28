<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    */

    'namespace' => 'Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    */

    'view' => 'resources/views/livewire',

    /*
    |--------------------------------------------------------------------------
    | Custom modules setup
    |--------------------------------------------------------------------------
    |
    */

    'custom_modules' => [
        'HR' => [
            'path'             => base_path('Modules/HR'),
            'module_namespace' => 'Modules\\HR',
            'namespace'        => 'App\\Livewire',
            'view'             => 'resources/views/livewire',
            'name_lower'       => 'hr',
        ],
         'Store' => [
            'path'             => base_path('Modules/Store'),
            'module_namespace' => 'Modules\\Store',
            'namespace'        => 'App\\Livewire',
            'view'             => 'resources/views/livewire',
            'name_lower'       => 'store',
        ],
         'Vehicles' => [
            'path'             => base_path('Modules/Vehicles'),
            'module_namespace' => 'Modules\\Vehicles',
            'namespace'        => 'App\\Livewire',
            'view'             => 'resources/views/livewire',
            'name_lower'       => 'vehicles',
        ],
        'Invoices' => [
            'path'             => base_path('Modules/Invoices'),
            'module_namespace' => 'Modules\\Invoices',
            'namespace'        => 'App\\Livewire',
            'view'             => 'resources/views/livewire',
            'name_lower'       => 'invoices',
        ],
           'Drivers' => [
            'path'             => base_path('Modules/Drivers'),
            'module_namespace' => 'Modules\\Drivers',
            'namespace'        => 'App\\Livewire',
            'view'             => 'resources/views/livewire',
            'name_lower'       => 'drivers',
        ],
    ],

];
