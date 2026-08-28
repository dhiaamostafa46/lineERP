<?php

return [
    'singular' => 'User devices',
    'plural'   => 'Users devices',
    'fields'   => [
        'id'               => '#',
        'user_id'          => 'User',
        'device_token'     => 'Device Token',
        'device_serial'    => 'Device Serial Number',
        'device_name'      => 'Device Name',
        'user_agent'       => 'User Agent',
        'ip_address'       => 'IP Address',
        'device_type'      => 'Device Type',
        'browser'          => 'Browser',
        'os'               => 'Operating System',
        'status'           => 'Status',
        'last_activity_at' => 'Last Activity',
        'created_at'       => 'Created At',
        'updated_at'       => 'Updated At',
        'exist'               => 'Device already registered, contact support', 
        'created'          => 'Device registered successfully, awaiting activation from administration',
        'approved'          => 'Device activated successfully',
        'not_registered'          => 'Device not registered, please click on device registration',
        'not_approved'          => 'Device not approved, please contact administration',
    ],
];
