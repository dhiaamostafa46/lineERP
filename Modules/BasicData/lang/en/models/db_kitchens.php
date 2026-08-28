<?php

return [
    'singular' => 'Kitchen',
    'plural'   => 'Kitchens',

    'fields'   => [
        'id'         => 'ID',
        'name'       => 'Kitchen Name',
        'orgID'      => 'Organization',
        'branchID'   => 'Branch',
        'userID'     => 'User',
        'barcode'    => 'Barcode',
        'status'     => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    'statuses' => [
        0 => 'Inactive',
        1 => 'Active',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
    ],
];
