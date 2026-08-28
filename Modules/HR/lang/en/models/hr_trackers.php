<?php

return [
    'singular' => 'Tracker',
    'plural'   => 'Trackers',
    'fields'   => [
        // Added automatically
        'apptovals' => 'Apptovals',

        'id'                => 'Id',
        'name'              => 'Name',
        'status'            => 'Status',
        'type'              => 'Type',
        'in_progress'       => 'In Progress',
        'steps'             => 'Steps',
        'jobs'              => 'Jobs',
        'department_id'     => 'Department',
        'tracker_approvals' => 'Tracker Approvals',
        'created_at'        => 'Created At',
        'updated_at'        => 'Updated At',
        'holidays'          => 'Holidays',
        'penalties'         => 'Penalties',
        'advances'          => 'Advances',
        'rewards'           => 'Rewards',
        'pending'           => 'Pending',
        'active'            => 'Active',
        'inactive'          => 'Inactive',
        'approve'           => 'Approve',
        'reject'            => 'Reject',
    ],
    'types' => [
        'holidays'  => 'Holidays',
        'penalties' => 'Penalties',
        'advances'  => 'Advances',
        'rewards'   => 'Rewards',
        'justifications' => 'justifications',
    ]
];
