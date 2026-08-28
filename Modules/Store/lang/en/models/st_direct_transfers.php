<?php

return [
    'singular' => 'Stock Transfer',
    'plural'   => 'Stock Transfers',

    'fields' => [
        'id'             => 'ID',
        'org_id'         => 'Organization',
        'branch_id'      => 'Branch',
        'user_id'        => 'User',
        'document_number'=> 'Document Number',
        'document_date'  => 'Document Date',
        'from_store_id'  => 'From Store',
        'to_store_id'    => 'To Store',
        'status'         => 'Status',
        'total_items'    => 'Total Items',
        'total_quantity' => 'Total Quantity',
        'total_value'    => 'Total Value',
        'notes'          => 'Notes',
        'product_name'   => 'Product Name',
        'transfer_type'  => 'Transfer Type',
        'received_quantity' => 'Received Quantity',
    ],

    'items' => [
        'product_id'         => 'Product',
        'unit_id'            => 'Unit',
        'quantity'           => 'Quantity',
        'unit_cost'          => 'Unit Cost',
        'total_cost'         => 'Total Cost',
        'notes'              => 'Notes',
        'received_quantity'  => 'Received Quantity',
    ],

    'status' => [
        'draft' => 'Draft',
        'in_transit' => 'In Transit',
        'transferred' => 'Transferred',
        'destination_draft' => 'Destination Draft',
        'completed' => 'Completed / Approved',
        'cancelled' => 'Cancelled',
        'partial_approved' => 'Partial Approval',
        'returned' => 'Returned',
        'partial_returned' => 'Partially Returned',
    ]
];
