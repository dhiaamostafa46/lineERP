<?php

return [
    'singular' => 'Stock Receive Voucher',
    'plural'   => 'Stock Receive Vouchers',

    'fields' => [
        'id' => 'ID',
        'org_id' => 'Organization',
        'branch_id' => 'Branch',
        'user_id' => 'User',
        'transfer_out_id' => 'Transfer Request',
        'document_number' => 'Document Number',
        'document_date' => 'Document Date',
        'received_date' => 'Received Date',
        'from_store_id' => 'From Store',
        'to_store_id' => 'To Store',
        'type' => 'Type',
        'status' => 'Status',
        'total_items' => 'Total Items',
        'total_quantity' => 'Total Quantity',
        'total_value' => 'Total Value',
        'variance_quantity' => 'Variance Quantity',
        'variance_value' => 'Variance Value',
        'approved_by' => 'Approved By',
        'approved_at' => 'Approved At',
        'received_by' => 'Received By',
        'received_at' => 'Received At',
        'journal_entry_id' => 'Journal Entry',
        'notes' => 'Notes',
    ],

    'items' => [
        'transfer_in_id' => 'Stock Receive Voucher',
        'transfer_out_item_id' => 'Transfer Request Item',
        'product_id' => 'Product',
        'unit_id' => 'Unit',
        'sent_quantity' => 'Sent Quantity',
        'received_quantity' => 'Received Quantity',
        'variance_quantity' => 'Variance Quantity',
        'unit_cost' => 'Unit Cost',
        'total_cost' => 'Total Cost',
        'have_sizes' => 'Have Sizes',
        'unit' => 'Unit',
        'status' => 'Status',
        'variance_reason' => 'Variance Reason',
        'notes' => 'Notes',

        'size_id' => 'size',
        'actual_quantity'    => 'Actual Quantity',
        'book_quantity'      => 'Book Quantity',
        'barcode' => ' barcode',
    ]
];
