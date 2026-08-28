<?php

return [
    'singular' => 'Unit',
    'plural'   => 'Units',

    'fields'   => [
        'id'                 => 'ID',
        'name'               => 'Unit Name',
        'org_id'             => 'Organization',
        'conversion_factor'  => 'Conversion Factor',
        'is_base'            => 'Base Unit',
        'status'             => 'Status',
        'is_virtual'         => 'Type',
        'created_at'         => 'Created At',
        'updated_at'         => 'Updated At',
    ],

    'statuses' => [
        0 => 'Inactive',
        1 => 'Active',
    ],

    'virtual_statuses' => [
        0 => 'Real Unit',
        1 => 'Virtual Unit',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
        'real'     => 'badge badge-primary',
        'virtual'  => 'badge badge-warning',
    ],
];
