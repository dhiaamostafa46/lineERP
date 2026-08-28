<?php

return [
  
    'singular' => 'Inventory First Term',
    'plural'   => 'Inventory First Term',

    'fields' => [
        'id'              => 'ID',
        'org_id'          => 'Organization',
        'branch_id'       => 'Branch',
        'user_id'         => 'User',
        'document_number' => 'Document Number',
        'document_date'   => 'Document Date',
        'store_id'        => 'Store',
        'status'          => 'Status',
        'type'            => 'Type',
        'total_items'     => 'Total Items',
        'total_quantity'  => 'Total Quantity',
        'total_value'     => 'Total Value',
        'approved_by'     => 'Approved By',
        'approved_at'     => 'Approved At',
        'notes'           => 'Notes',
    ],


    'items' => [
        'opening_balance_id' => 'Inventory First Term',
        'product_id'         => 'Product',
        'barcode'            => 'barcode',
        'unit_id'            => 'Unit',
        'size_id'            => 'Size',
        'quantity'           => 'Quantity',
        'actual_quantity'    => 'Actual Quantity',
         'book_quantity'      => 'Book Quantity',
        'unit_cost'          => 'Unit Cost',
        'total_cost'         => 'Total Cost',
        'status'             => 'Status',
        'notes'              => 'Notes',
    ],

    'import' => [
        'title' => 'Import Opening Balance',
        'upload_title' => 'Upload Opening Balance File',
        'select_file' => 'Select Excel File',
        'file_help' => 'File must be in .xlsx, .xls, or .csv format and contain the required data as in the template.',
        'download_template' => 'Download Tutorial Template',
        'template_help' => 'Use the template to ensure data compatibility with the system',
        'important_notes' => 'Important Instructions:',
        'store_names' => 'Store Names',
        'store_names_help' => 'The store name in the file must exactly match the name registered in the system.',
        'new_products' => 'New Products',
        'new_products_help' => 'If the product does not exist, the system will automatically create it as a new product.',
        'units_categories' => 'Units and Categories',
        'units_categories_help' => 'Units and categories are searched by name; if they do not exist, they will be created.',
        'barcode_help' => 'Can be left blank for the system to generate it automatically.',
        'start_upload' => 'Start Upload Process',
    ]
];
