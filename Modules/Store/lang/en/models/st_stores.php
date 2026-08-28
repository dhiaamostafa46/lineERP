<?php

return [
    'singular' => 'Store',
    'plural' => 'Stores',

    'fields' => [
        'id' => 'ID',
        'name' => 'Store Name',
        'branch_id' => 'Branch',
        'manager_user_id' => 'Manager',
        'org_id' => 'Organization',
        'type' => 'Store Type',
        'status' => 'Status',
        'location' => 'Location',
        'address' => 'Address',
        'is_active' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    'statuses' => [
        0 => 'Inactive',
        1 => 'Active',
    ],

    'types' => [
        'main' => 'Main',
        'secondary' => 'Secondary',
        'quarantine' => 'Quarantine',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active' => 'badge badge-success',
        'main' => 'badge badge-primary',
        'secondary' => 'badge badge-warning',
        'quarantine' => 'badge badge-info',
    ],
];
