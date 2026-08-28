<?php

return [
    'singular' => 'Stock Issue Voucher',
    'plural'   => 'Stock Issue Vouchers',

    'fields' => [
        'id' => 'ID',
        'org_id' => 'Organization',
        'branch_id' => 'Branch',
        'user_id' => 'User',
        'document_number' => 'Document Number',
        'document_date' => 'Document Date',
        'from_store_id' => 'From Store',
        'to_store_id' => 'To Store',
        'status' => 'Status',
        'type' => 'Type',
        'total_items' => 'Total Items',
        'total_quantity' => 'Total Quantity',
        'total_value' => 'Total Value',
        'approved_by' => 'Approved By',
        'approved_at' => 'Approved At',
        'sent_by' => 'Sent By',
        'sent_at' => 'Sent At',
        'journal_entry_id' => 'Journal Entry',
        'notes' => 'Notes',
    ],

    'items' => [
        'transfer_out_id' => 'Stock Issue Voucher',
        'product_id' => 'Product',
        'unit_id' => 'Unit',
        'quantity' => 'Quantity',
        'unit_cost' => 'Unit Cost',
        'total_cost' => 'Total Cost',
        'have_sizes' => 'Have Sizes',
        'unit' => 'Unit',
        'status' => 'Status',
        'notes' => 'Notes',
        'size_id' => 'Size ID',
        'actual_quantity' => 'Actual Quantity',
        'book_quantity' => 'Book Quantity',
        'barcode' => 'Barcode',
    ]
];
