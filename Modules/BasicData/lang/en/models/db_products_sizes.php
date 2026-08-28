<?php

return [
    'singular' => 'Product Size',
    'plural'   => 'Product Sizes',

    'fields'   => [
        'id'                           => 'ID',
        'name'                         => 'Size Name',
        'product_id'                   => 'Product',
        'sale_price'                   => 'Sale Price',
        'cost_price'                   => 'Cost Price',
        'base_unit_consumption_factor' => 'Consumption Factor',
        'barcode'                      => 'Barcode',
        'status'                       => 'Status',
        'org_id'                       => 'Organization',
        'created_at'                   => 'Created At',
        'updated_at'                   => 'Updated At',
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
