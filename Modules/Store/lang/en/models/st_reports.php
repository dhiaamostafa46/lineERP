<?php

return [

    /* =========================
     |  General
     ========================= */
    'singular' => 'Report',
    'plural' => 'Reports',

    /* =========================
     |  General Fields
     ========================= */
    'fields' => [
        'id' => 'ID',
        'report_type' => 'Report Type',
        'date_from' => 'From Date',
        'date_to' => 'To Date',
        'account_id' => 'Account',
        'account_from' => 'From Account',
        'account_to' => 'To Account',
        'cost_center_id' => 'Cost Center',
        'branch_id' => 'Branch',
        'currency' => 'Currency',
        'posted_only' => 'Posted Entries Only',
        'created_at' => 'Created At',
        'created_by' => 'Created By',
    ],

    /* =========================
     |  Report Types
     ========================= */
    'types' => [
        'all_reports' => 'All Reports',
        'account_statement' => 'Account Statement',
        'account_summary' => 'Account Summary',
        'income_statement' => 'Income Statement',
        'trial_balance' => 'Trial Balance',
        'general_ledger' => 'General Ledger',
        'journal_entries' => 'Journal Entries',
        'balance_sheet' => 'Balance Sheet',
        'cash_flow_statement_indirect' => 'Cash Flow Statement (Indirect)',
        'cash_flow_statement_direct' => 'Cash Flow Statement (Direct)',
        'tax' => 'Tax Report',
        'cost_center' => 'Cost Center Report',
        'assets' => 'Assets Report',
        'sales' => 'Sales Report',
        'purchases' => 'Purchases Report',
        'sales_tax_report' => 'Sales Tax Report',
        'purchases_tax_report' => 'Purchases Tax Report',
        // Store inventory reports
        'stock_movement' => 'Stock Movement',
        'stock_balance' => 'Stock Balance',
        'inventory_valuation' => 'Inventory Valuation',
        'low_stock' => 'Low Stock',
        'inventory_count' => 'Inventory Count',
        'pending_stock' => 'Pending / Reserved Stock',
    ],

    /* =========================
     |  Report Names
     ========================= */
    'reports' => [
        'account_statement' => 'Account Statement',
        'general_ledger' => 'General Ledger',
        'trial_balance_totals' => 'Trial Balance Totals',
        'trial_balance_balances' => 'Trial Balance',
        'account_total_balance' => 'Account Total Balance',
        'income_statement' => 'Income Statement',
        'balance_sheet' => 'Balance Sheet',
        'cost_centers_report' => 'Cost Centers Report',
        'cost_centers_calculation_report' => 'Cost Centers Report - Accounts',
    ],

    /* =========================
     |  Table Columns
     ========================= */
    'columns' => [
        // Basic data
        'id' => '#',
        'date' => 'Date',
        'entry_number' => 'Entry No.',
        'reference' => 'Reference',
        'description' => 'Description',
        'account_code' => 'Account Code',
        'account_name' => 'Account Name',
        'account_type' => 'Account Type',
        'cost_center' => 'Cost Center',
        'cost_center_name' => 'Cost Center Name',
        'cost_center_code' => 'Cost Center Code',
        'account' => 'Account',
        // Accounting movements
        'debit' => 'Debit',
        'credit' => 'Credit',
        // Balances
        'opening_balance' => 'Opening Balance',
        'period_balance' => 'Period Balance',
        'closing_balance' => 'Closing Balance',
        'balance' => 'Balance',
        'running_balance' => 'Running Balance',
        // Totals
        'total_debit' => 'Total Debit',
        'total_credit' => 'Total Credit',
        'total' => 'Total',
        // Additional accounting
        'previous_balance' => 'Previous Balance',
        'current_balance' => 'Current Balance',
        'net_balance' => 'Net Balance',
        'movement' => 'Movement',
        'generated_at' => 'Generated At',
        'unposted_pl' => 'Unposted P&L',
        // Periods
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'period' => 'Period',
        'fiscal_year' => 'Fiscal Year',
        // Store inventory columns
        'movement_number' => 'Movement No.',
        'movement_type' => 'Movement Type',
        'product' => 'Product',
        'category' => 'Category',
        'store' => 'Store',
        'quantity' => 'Quantity',
        'unit' => 'Unit',
        'unit_cost' => 'Unit Cost',
        'total_cost' => 'Total',
        'total_value' => 'Total Value',
        'total_quantity' => 'Total Quantity',
        'current_quantity' => 'Current Qty',
        'reserved_quantity' => 'Reserved Qty',
        'available_quantity' => 'Available Qty',
        'average_cost' => 'Avg. Cost',
        'min_quantity' => 'Min Qty',
        'reorder_point' => 'Reorder Point',
        'shortage' => 'Shortage',
        'pending_quantity' => 'Pending Qty',
        'from_store' => 'From Store',
        'to_store' => 'To Store',
        'last_movement' => 'Last Movement',
        'status' => 'Status',
        'opening_quantity' => 'Opening Qty',
        'qty_in' => 'Qty In',
        'qty_out' => 'Qty Out',
        'book_quantity' => 'Book Qty',
        'actual_quantity' => 'Actual Qty',
        'difference' => 'Difference',
    ],

    /* =========================
     |  Filters
     ========================= */
    'filters' => [
        'date_range' => 'Period',
        'date_from' => 'From Date',
        'date_to' => 'To Date',
        'account' => 'Account',
        'select_account' => 'Select Account',
        'cost_center' => 'Cost Center',
        'all_cost_centers' => 'All Cost Centers',
        'all_branch' => 'All Branches',
        'branch' => 'Branch',
        'currency' => 'Currency',
        'branchId' => 'Branch',
        'entry_status' => 'Entry Status',
        'fiscalYear' => 'Fiscal Year',
        'current_fiscal_year' => 'Current Fiscal Year',
        // Store filters
        'store' => 'Store',
        'all_stores' => 'All Stores',
        'product' => 'Product',
        'all_products' => 'All Products',
        'movement_type' => 'Movement Type',
        'all_types' => 'All Types',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'category' => 'Category',
        'all_categories' => 'All Categories',
        'as_of_date' => 'As of Date',
        'product_name' => 'Product Name',
        'search_by_product_name' => 'Search product name (full or partial)',
        'product_display' => 'Product Display',
        'all_with_zero' => 'Items with Zero Balance',
        'without_zero' => 'Items without Zero Balance',
        'zero_only' => 'Zero Balance Items Only',
    ],

    /* =========================
     |  Entry Statuses
     ========================= */
    'statuses' => [
        'all' => 'All',
        'draft' => 'Draft',
        'posted' => 'Posted',
        'reversed' => 'Reversed',
    ],

    /* =========================
     |  Actions
     ========================= */
    'actions' => [
        'generate' => 'Generate Report',
        'preview' => 'Preview',
        'print' => 'Print',
        'export' => 'Export',
    ],

    /* =========================
     |  Messages
     ========================= */
    'messages' => [
        'no_data' => 'No data found for the selected criteria.',
        'loading' => 'Generating report, please wait...',
        'invalid_date' => 'The specified date range is invalid.',
        'select_account' => 'Please select an account',
        'select_account_description' => 'Select an account from the list above to view its statement',
        'no_transactions' => 'No transactions found for the selected account and period',
        // Store messages
        'select_filters' => 'Please set the filters and click Search to display the report',
        'low_stock_warning' => 'Warning: :count item(s) are below minimum stock level and need reordering',
        'no_low_stock' => 'All items are within acceptable stock levels',
    ],

    /* =========================
     |  Labels
     ========================= */
    'labels' => [
        'new' => 'New!',
    ],

    /* =========================
     |  Stock Status
     ========================= */
    'status' => [
        'normal' => 'Normal',
        'low' => 'Low',
        'empty' => 'Out of Stock',
    ],

    /* =========================
     |  Totals
     ========================= */
    'totals' => [
        'total_revenue' => 'Total Revenue',
        'total_expense' => 'Total Expenses',
        'net_income' => 'Net Income',
    ],

];
