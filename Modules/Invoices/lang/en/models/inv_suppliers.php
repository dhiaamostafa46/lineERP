<?php

return [
    'singular' => 'Supplier',
    'plural'   => 'Suppliers',

    'fields'   => [
        'id'                => 'ID',
        'name'              => 'Supplier Name',
        'phone'             => 'Phone Number',
        'email'             => 'Email Address',
        'vat_number'        => 'VAT Number',
        'cr_number'         => 'Commercial Registration No.',
        'address'           => 'Detailed Address',

        'country'           => 'Country',
        'city'              => 'City',
        'district'          => 'District',
        'street'            => 'Street',
        'building_number'   => 'Building Number',
        'postal_code'       => 'Postal Code',
        'additional_number'=> 'Additional Number',

        'tree_account_id'   => 'Linked Financial Account',
        'branch_id'         => 'Branch',
        'credit_limit'      => 'Credit Limit',
        'status'            => 'Status',

        'file'              => 'Attachment',
        'created_at'        => 'Created At',
    ],

    'sections' => [
        'contact_info'   => 'Contact Information',
        'tax_info'       => 'Tax Information',
        'address_info'   => 'Address Information',
        'financial_info' => 'Financial Information',
        'attachments'    => 'Attachments',
    ],
];
