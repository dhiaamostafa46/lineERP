<?php

return [
    'singular' => 'Package',
    'plural'   => 'Packages',
    'description' => 'Packages are based on the number of users',
    'note'        => 'Note',
    'fields'   => [
        'id'                          => '#',
        'users_count'                 => 'Number of Users',
        'subscription'                => 'Subscription',
        'from'                        => 'From',
        'to'                          => 'To',
        'price'                       => 'Price',
        'user'                        => 'users',
        'SAR'                         => 'SAR',
        'up'                          => 'up',
        'activate_yearly_subscription'=> 'Activate Yearly Subscription',
        'payment'                     => 'Payment',
        'user_price'                  => 'User Price',
        'package_expiry_date'         => 'Package Expiry Date', // New
    ],

    'message'=>[
        'Payment_failed' =>'Payment failed',
        'Payment_successfully' =>'Payment processed successfully',
        'process_failed' =>'Failed to process payment',
        'Payment_type'   =>'Invalid payment type',
    ]
];
