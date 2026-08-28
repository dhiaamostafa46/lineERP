<?php

return [
    'singular' => 'Stock Receiving',
    'plural'   => 'Stock Receiving',

    'fields' => [
        'id'             => 'ID',
        'org_id'         => 'Organization',
        'branch_id'      => 'Branch',
        'user_id'        => 'User',
        'document_number'=> 'Document Number',
        'document_date'  => 'Document Date',
        'store_id'       => 'Store',
        'status'         => 'Status',
        'total_items'    => 'Total Items',
        'total_quantity' => 'Total Quantity',
        'total_value'    => 'Total Value',
        'notes'          => 'Notes',
        'product_name'   => 'Product Name',
        'tree_account_id'=> 'Offset Account',
    ],

    'items' => [
        'product_id'         => 'Product',
        'unit_id'            => 'Unit',
        'quantity'           => 'Quantity',
        'unit_cost'          => 'Unit Cost',
        'total_cost'         => 'Total Cost',
        'notes'              => 'Notes',
    ]
];
