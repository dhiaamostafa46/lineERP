<?php

return [
    'singular' => 'Account',
    'plural'   => 'Chart of Accounts',

    /*
    |--------------------------------------------------------------------------
    | Account Types
    |--------------------------------------------------------------------------
    */
    'types' => [
        'asset'         => 'Assets',
        'liability'     => 'Liabilities',
        'equity'        => 'Equity',
        'revenue'       => 'Revenue',
        'expense'       => 'Expenses',
        'cost_of_sales' => 'Cost of Sales',
        'suppliers'     => 'Suppliers',
        'treasury'      => 'Treasury',
        'bank'          => 'Bank',
        'inventory'     => 'Inventory',
        'customers'     => 'Customers',
        'sales'         => 'Sales',
        'purchases'     => 'Purchases',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Nature
    |--------------------------------------------------------------------------
    */
    'nature' => [
        'debit'  => 'Debit',
        'credit' => 'Credit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fields
    |--------------------------------------------------------------------------
    */
    'fields' => [
        'code'         => 'Account Code',
        'name'         => 'Account Name',
        'account_type' => 'Account Type',
        'parent_id'    => 'Parent Account',
        'level'        => 'Level',
        'is_leaf'      => 'Leaf Account',
        'status'       => 'Status',
        'is_system'    => 'System Account',
        'type'         => 'Account Nature',
        'description'  => 'Description',
        'attributes'   => 'Additional Attributes',
        'created_at'   => 'Created At',
        'updated_at'   => 'Updated At',
        'use_cost_center'  => 'Use Cost Center',
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuses
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ],
];
