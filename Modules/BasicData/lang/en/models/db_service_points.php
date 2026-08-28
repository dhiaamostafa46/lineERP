<?php

return [
    'singular' => 'Service Point',
    'plural'   => 'Service Points',

    'fields'   => [
        'id'         => 'ID',
        'name'       => 'Name',
        'orgID'      => 'Organization',
        'branchID'   => 'Branch',
        'userID'     => 'User',
        'code'       => 'Code',
        'type'       => 'Type',
        'status'     => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    'statuses' => [
        0 => 'Inactive',
        1 => 'Active',
    ],

    'types' => [
        1 => 'Table',
        2 => 'Drive',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
    ],
];
