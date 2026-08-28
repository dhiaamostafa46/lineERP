<?php

return [
    'singular' => 'Bond',
    'plural' => 'Bonds',
    'fields' => [
        'id' => 'ID',
        'bond_type' => 'Bond Type',
        'voucher_number' => 'Voucher Number',
        'reference_number' => 'Reference Number',
        'date' => 'Date',
        'amount' => 'Amount',
        'fund_account_id' => 'Fund Account (Cash/Bank)',
        'contact_account_id' => 'Contact Account',
        'cost_center_id' => 'Cost Center',
        'branch_id' => 'Branch',
        'description' => 'Description / Remarks',
        'status' => 'Status',
        'attachment' => 'Attachment',
        'journal_entry_id' => 'Journal Entry ID',
        'created_by' => 'Created By',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'pay_to_receive_from' => 'Pay to / Receive from',
        'amount_text'        => 'Amount',
        'sar_only'           => 'Saudi Riyal Only',
        'for_purpose'        => 'For Purpose of',
        'accountant'         => 'Accountant',
        'receiver'           => 'Receiver',
        'manager'            => 'General Manager',
    ],
    'types' => [
        'payment' => 'Payment Voucher',
        'receipt' => 'Receipt Voucher',
    ],

    'sections'=>[
        'account_details'=> 'Account Details',
        'other_info' =>'Other Info',
    ],

    'statuses' => [
        'draft' => 'Draft',
        'approved' => 'Approved',
    ],
];
