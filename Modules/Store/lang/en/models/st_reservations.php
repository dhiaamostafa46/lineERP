<?php

return [
    'singular' => 'Stock Reservation',
    'plural' => 'Stock Reservations',
    'fields' => [
        'id' => 'ID',
        'user_id' => 'User',
        'document_number' => 'Document Number',
        'document_date' => 'Date',
        'store_id' => 'Warehouse',
        'status' => 'Status',
        'total_items' => 'Total Items',
        'total_quantity' => 'Total Quantity',
        'total_value' => 'Total Value',
        'notes' => 'Notes',
        'items' => 'Reserved Items',
        'product_id' => 'Product',
        'unit_id' => 'Unit',
        'quantity' => 'Quantity',
        'unit_cost' => 'Unit Cost',
        'total_cost' => 'Total Cost',
    ],
    'status' => [
        'draft' => 'Draft',
        'reserved' => 'Reserved',
        'returned' => 'Returned to Warehouse',
        'cancelled' => 'Cancelled',
    ],
    'messages' => [
        'authorized_success' => 'Reservation authorized successfully',
        'returned_success' => 'Stock returned to warehouse successfully',
    ]
];
