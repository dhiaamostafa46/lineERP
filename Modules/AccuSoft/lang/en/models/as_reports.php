<?php

return [

    /* =========================
     |  General
     ========================= */
    'singular' => 'Report',
    'plural'   => 'Reports',

    /* =========================
     |  General Fields
     ========================= */
    'fields' => [
        'id'             => 'ID',
        'report_type'    => 'Report Type',
        'date_from'      => 'From Date',
        'date_to'        => 'To Date',
        'account_id'     => 'Account',
        'account_from'   => 'From Account',
        'account_to'     => 'To Account',
        'cost_center_id' => 'Cost Center',
        'branch_id'      => 'Branch',
        'currency'       => 'Currency',
        'posted_only'    => 'Posted Entries Only',
        'created_at'     => 'Created At',
        'created_by'     => 'Created By',
    ],

    /* =========================
     |  Report Types
     ========================= */
    'types' => [
        'all_reports'                  => 'All Reports',
        'account_statement'            => 'Account Statement',
        'account_summary'              => 'Account Summary',
        'income_statement'             => 'Income Statement',
        'trial_balance'                => 'Trial Balance',
        'general_ledger'               => 'General Ledger',
        'journal_entries'              => 'Journal Entries',
        'balance_sheet'                => 'Balance Sheet',
        'cash_flow_statement_indirect' => 'Cash Flow Statement',
        'cash_flow_statement_direct'   => 'Cash Flow Statement',
        'tax'                           => 'Tax Report',
        'cost_center'                  => 'Cost Center Report',
        'assets'                       => 'Assets Report',
        'sales'                        => 'Sales Report',
        'purchases'                    => 'Purchases Report',
        'sales_tax_report'             => 'Sales Tax Report',
        'purchases_tax_report'         => 'Purchases Tax Report',
    ],

    /* =========================
     |  Report Names
     ========================= */
    'reports' => [
        'account_statement'               => 'Account Statement',
        'general_ledger'                  => 'General Ledger',
        'trial_balance_totals'            => 'Trial Balance Totals',
        'trial_balance_balances'          => 'Trial Balance',
        'journal_entries'                 => 'Journal Entry Details',
        'account_total_balance'           => 'Account Total Balance',
        'income_statement'                => 'Income Statement',
        'balance_sheet'                   => 'Balance Sheet',
        'cost_centers_report'             => 'Cost Centers Report',
        'cost_centers_calculation_report' => 'Cost Centers Calculation Report',
        'assets'                          => 'Assets Report',
    ],

    /* =========================
     |  Table Columns
     ========================= */
    'columns' => [

        // Basic Info
        'id'               => 'ID',
        'date'             => 'Date',
        'entry_number'     => 'Entry Number',
        'reference'        => 'Reference',
        'description'      => 'Description',
        'account_code'     => 'Account Code',
        'account_name'     => 'Account Name',
        'account_type'     => 'Account Type',
        'account'          => 'Account',
        'cost_center'      => 'Cost Center',
        'cost_center_code' => 'Cost Center Code',
        'cost_center_name' => 'Cost Center Name',

        // Transactions
        'debit'            => 'Debit',
        'credit'           => 'Credit',

        // Balances
        'opening_balance'  => 'Opening Balance',
        'period_balance'   => 'Period Balance',
        'closing_balance'  => 'Closing Balance',
        'balance'          => 'Balance',
        'running_balance'  => 'Running Balance',

        // Totals
        'total_debit'      => 'Total Debit',
        'total_credit'     => 'Total Credit',
        'total'            => 'Total',
        'unposted_pl'     => 'Unposted Profit and Loss',

        // Additional
        'previous_balance' => 'Previous Balance',
        'current_balance'  => 'Current Balance',
        'net_balance'      => 'Net Balance',
        'movement'         => 'Movement',
        'generated_at'     => 'Generated At',

        // Periods
        'from_date'        => 'From Date',
        'to_date'          => 'To Date',
        'period'           => 'Period',
        'fiscal_year'      => 'Fiscal Year',
    ],

    /* =========================
     |  Filters
     ========================= */
    'filters' => [
        'date_range'         => 'Date Range',
        'date_from'          => 'From Date',
        'date_to'            => 'To Date',
        'account'            => 'Account',
        'select_account'     => 'Select Account',
        'cost_center'        => 'Cost Center',
        'all_cost_centers'   => 'All Cost Centers',
        'branch'             => 'Branch',
        'branchId'           => 'Branch',
        'all_branch'         => 'All Branches',
        'entry_type'         => 'Entry Type',
        'all_types'          => 'All Types',
        'currency'           => 'Currency',
        'entry_status'       => 'Entry Status',
        'fiscalYear'         => 'Fiscal Year',
        'current_fiscal_year'=> 'Current Fiscal Year',
    ],

    /* =========================
     |  Entry Statuses
     ========================= */
    'statuses' => [
        'all'      => 'All',
        'draft'    => 'Draft',
        'posted'   => 'Posted',
        'reversed' => 'Reversed',
    ],

    /* =========================
     |  Actions
     ========================= */
    'actions' => [
        'generate' => 'Generate Report',
        'preview'  => 'Preview',
        'print'    => 'Print',
        'export'   => 'Export',
    ],

    /* =========================
     |  Messages
     ========================= */
    'messages' => [
        'no_data'                    => 'No data available for the selected criteria.',
        'loading'                    => 'Generating report, please wait...',
        'invalid_date'               => 'The selected date range is invalid.',
        'select_account'             => 'Please select an account',
        'select_account_description' => 'Select an account from the list above to view its account statement',
        'no_transactions'            => 'No transactions found for the selected account during the chosen period',
    ],

    /* =========================
     |  Labels
     ========================= */
    'labels' => [
        'new' => 'New!',
    ],

    'cash_flow' => [
        'operating_activities' => 'Operating Activities',
        'investing_activities' => 'Investing Activities',
        'financing_activities' => 'Financing Activities',
        'net_income'           => 'Net Income',
        'depreciation'         => 'Depreciation of Fixed Assets',
        'receivables'          => 'Change in Accounts Receivable',
        'inventory'            => 'Change in Inventory',
        'other_assets'         => 'Change in Other Current Assets',
        'payables'             => 'Change in Accounts Payable',
        'other_liabilities'    => 'Change in Other Current Liabilities',
        'fixed_assets'         => 'Change in Fixed Assets',
        'loans'                => 'Change in Loans',
        'capital'              => 'Change in Capital',
        'net_cash_operating'   => 'Net Cash from Operating Activities',
        'net_cash_investing'   => 'Net Cash from Investing Activities',
        'net_cash_financing'   => 'Net Cash from Financing Activities',
        'net_change_in_cash'   => 'Net Change in Cash',
        'beginning_cash'       => 'Cash at Beginning of Period',
        'ending_cash'          => 'Cash at End of Period',
    ],

    /* =========================
     |  Totals
     ========================= */
    'totals' => [
        'total_revenue' => 'Total Revenue',
        'total_expense' => 'Total Expense',
        'net_income'    => 'Net Income',
        'total_assets'  => 'Total Assets',
        'total_liabilities' => 'Total Liabilities',
        'total_equity'  => 'Total Equity',
        'total_liabilities_and_equity' => 'Total Liabilities & Equity',
        'difference'    => 'Difference',
    ],

];
