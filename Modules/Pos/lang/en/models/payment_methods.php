<?php

return [
    'singular' => 'Payment Method',
    'plural' => 'Payment Methods',

    'types' => [
        'cash' => 'Cash',
        'card' => 'Card',
        'transfer' => 'Transfer',
        'credit' => 'Credit',
        'installment' => 'Multiple Payments',
    ],

    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'type' => 'Type',
        'account_id' => 'Account',
        'is_active' => 'Is Active',
        'status' => 'Status',
        'select_account' => 'Select Account',
        'type_cash' => 'Cash',
        'type_bank' => 'Bank Transfer',
        'type_credit' => 'Credit',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],
];
