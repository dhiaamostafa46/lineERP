<?php

return [
    'singular' => 'Opening Balance',
    'plural'   => 'Opening Balances',

    'fields' => [
        'id'           => 'ID',
        'store_id'     => 'Store',
        'date'         => 'Date',
        'notes'        => 'Notes',
        'status'       => 'Status',
        'total_cost'   => 'Total Cost',
        'created_at'   => 'Created At',
        'updated_at'   => 'Updated At',
        'items'        => 'Items',
        'product_id'   => 'Product',
        'quantity'     => 'Quantity',
        'unit_cost'    => 'Unit Cost',
        'expiry_date'  => 'Expiry Date',
        'batch_number' => 'Batch Number',
        'org_id'       => 'Organization ID',
        'branch_id'    => 'Branch',
        'user_id'      => 'User ID',
        'adjustment_number' => 'Adjustment Number',
        'adjustment_date' => 'Adjustment Date',
        'type'         => 'Type',
        'sub_type'     => 'Sub Type',
        'reason'       => 'Reason',
        'total_quantity' => 'Total Quantity',
        'total_value'  => 'Total Value',
        'created_by'   => 'Created By',
        'approved_by'  => 'Approved By',
        'approved_at'  => 'Approved At',
    ],
    'pages' => [
        'openingbalance' => 'Opening Balance',
    ],

    'list_types' => [
        'all'          => 'All',
        'opening'      => 'Opening Balance',
        'damaged'      => 'Damaged Stock',
        'settlement'   => 'Stock Adjustment',
        'inventory'    => 'Stock Inventory',
        'transfer_out' => 'Transfer Out',
        'transfer_in'  => 'Transfer In',
    ],
];
