<?php

return [
    'singular' => 'Driver Company Reference',
    'plural' => 'Driver Company References',
    'fields' => [
        'driver_id' => 'Driver',
        'company_id' => 'Company',
        'ref_no' => 'Reference No.',
        'status' => 'Status',
        'started_at' => 'Started At',
        'ended_at' => 'Ended At',
        'created_by' => 'Created By',
        'updated_by' => 'Last Updated By',
    ],
    'statuses' => [
        'active' => 'Active',
        'completed' => 'Completed',
        'suspended' => 'Suspended',
    ],
    'messages' => [
        'no_active_ref' => 'This driver has no active company reference number.',
    ],
];
