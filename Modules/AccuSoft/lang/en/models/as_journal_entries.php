<?php

return [

    /* =========================
     |  General
     ========================= */
    'singular' => 'Journal Entry',
    'plural'   => 'Journal Entries',
    'balanced'   => 'Balanced',
    'unbalanced' => 'Unbalanced',

    /* =========================
     |  Fields (Journal Entry)
     ========================= */
    'fields' => [
        'id'             => 'ID',
        'entry_number'   => 'Entry Number',
        'entry_date'     => 'Entry Date',
        'reference'      => 'Reference',
        'description'    => 'Description',
        'notes'          => 'Notes',
        'attachment'     => 'Attachment',
        'account_id'     => 'Account',
        'cost_center_id' => 'Cost Center',
        'debit'          => 'Debit',
        'credit'         => 'Credit',
        'balance'        => 'Balance',
        'total'          => 'Total',
        'entry_type'     => 'Entry Type',
        'source'         => 'Entry Source',
        'total_debit'    => 'Total Debit',
        'total_credit'   => 'Total Credit',
        'status'         => 'Status',
        'is_locked'      => 'Is Locked',
        'locked_at'      => 'Locked At',
        'created_by'     => 'Created By',
        'created_at'     => 'Created At',
        'posted_by'      => 'Posted By',
        'posted_at'      => 'Posted At',
        'branch_id'      => 'Branch',
        'fiscal_year_id' => 'Fiscal Year',
        'approved_by'    => 'Approved By',
        'original_voucher' => 'Original Voucher',
        'amount_in_words' => 'Amount in Words',
        'accountant'     => 'Accountant',
        'reviewed_by'    => 'Internal Audit',
        'account_code'   => 'Account Code',
    ],

    /* =========================
     |  Entry Sources
     ========================= */
    'sources' => [
        'manual'    => 'Manual / General',
        'sales'     => 'Sales',
        'purchases' => 'Purchases',
        'store'     => 'Inventory / Store',
        'vehicles'  => 'Vehicles',
        'drivers'   => 'Drivers',
        'hr'        => 'HR & Payroll',
        'finance'   => 'Finance & Bonds',
        'assets'    => 'Fixed Assets',
        'pos'       => 'Point of Sale (POS)',
        'closing'   => 'Period Closing',
    ],

    /* =========================
     |  Entry Types
     ========================= */
    'types' => [
        'manual'     => 'Manual',
        'auto'       => 'Automatic',
        'opening'    => 'Opening Entry',
        'closing'    => 'Closing Entry',
        'depreciation' => 'Depreciation',
        'adjustment' => 'Adjustment Entry',
    ],

    /* =========================
     |  Entry Statuses
     ========================= */
    'statuses' => [
        'draft'    => 'Draft',
        'posted'   => 'Posted',
        'reversed' => 'Reversed',
        'locked'   => 'Locked',
        'pending'  => 'Pending',
    ],

    /* =========================
     |  Journal Entry Details
     ========================= */
    'details' => [
        'journal_entry_id' => 'Journal Entry',
        'tree_account_id'  => 'Account',
        'cost_center_id'   => 'Cost Center',
        'debit'            => 'Debit',
        'credit'           => 'Credit',
        'description'      => 'Description',
        'is_locked'        => 'Locked',
        'locked_at'        => 'Locked At',
        'locked_by'        => 'Locked By',
    ],

    'validations' => [
        'select_account_first' => 'Please select an account in the last row before adding a new row.',
        'min_two_rows'         => 'Journal entry must contain at least two rows.',
        'account_required_all_rows' => 'The account field is required in all rows.',
        'account_not_found'    => 'The selected account does not exist.',
        'cost_center_not_found' => 'The selected cost center does not exist.',
        'debit_numeric'        => 'The debit amount must be a number.',
        'credit_numeric'       => 'The credit amount must be a number.',
        'amount_required'      => 'A value must be entered in debit or credit.',
        'unbalanced_detailed'  => 'The entry is unbalanced. Total Debit: %s, Total Credit: %s',
        'zero_total_error'     => 'Cannot save an entry with a total of zero.',
        'cost_center_required_for_account' => 'Account (%s) requires selecting a cost center.',
    ],

    'messages' => [
        'balanced_error' => 'Entry is not balanced, Total Debit must equal Total Credit.',
        'details_required' => 'At least two detail rows are required.',
        'select_account' => 'Select Account',
        'no_results'     => 'No results found',
        'min_rows'       => 'Must have at least two rows',
        'alert'          => 'Alert',
        'min_rows_alert' => 'You must add at least two rows',
        'ok'             => 'OK',
        'optional'       => 'Optional',
        'loading_more'   => 'Loading more results...',
        'cost_center_required' => 'Cost center required',
        'cost_center_missing'  => 'Please select a cost center for the following accounts: ',
        'row'                  => 'Row',
        'account_required_for_amount' => 'An account must be selected when entering an amount',
        'no_both_debit_credit' => 'Cannot enter both debit and credit on the same row',
        'debit_exceeds'        => 'Debit exceeds',
        'credit_exceeds'       => 'Credit exceeds',
        'debit_extra'          => 'Extra debit',
        'credit_extra'         => 'Extra credit',
        'no_amounts_entered'   => 'No amounts entered',
        'difference'           => 'Difference',
        'basic_info'           => 'Basic Information',
    ],

    'import' => [
        'title'             => 'Import Journal Entries',
        'page_heading'      => 'Import Journal Entries from Excel File',
        'breadcrumb'        => 'Import',
        'download_template' => 'Download Excel Template',
        'upload_file'       => 'Upload Data File',
        'important_notes'   => 'Important Notes:',
        'note_1'            => 'Please ensure the file follows the approved template.',
        'note_2'            => 'Entries must be balanced (Total Debit = Total Credit for each entry).',
        'note_3'            => 'Account codes must match those in the Chart of Accounts.',
        'note_4'            => 'If there are errors, only correct entries will be imported, and you will be provided with an error file.',
        'choose_file'       => 'Choose Excel File (.xlsx, .xls, .csv)',
        'cancel'            => 'Cancel',
        'start_import'      => 'Start Import',
    ],
];
