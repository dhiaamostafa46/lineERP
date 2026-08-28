<?php

return [
    'singular' => 'Product Category',
    'plural'   => 'Product Categories',

    'fields'   => [
        'id'          => 'ID',
        'name'        => 'Name',
        'org_id'      => 'Organization',
        'user_id'     => 'User',
        'img'         => 'Image',
        'sort'        => 'Sort Order',
        'status'      => 'Status',
        'is_virtual'  => 'Category Type',
        'parent_id'   => 'Parent Category',
        'type'        => 'Used in Sales',
        'created_at'  => 'Created At',
        'updated_at'  => 'Updated At',
    ],

    'statuses' => [
        0 => 'Inactive',
        1 => 'Active',
    ],

    'virtual_statuses' => [
        0 => 'Real Category',
        1 => 'Virtual Category',
    ],

    'type_statuses' => [
        'Visible'=> 'Visible',
        'Hidden' => 'Hidden',
    ],

    'badges' => [
        'inactive' => 'badge badge-danger',
        'active'   => 'badge badge-success',
        'real'     => 'badge badge-primary',
        'virtual'  => 'badge badge-warning',
    ],
];
