<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Model Names
    |--------------------------------------------------------------------------
    */

    'singular' => 'Accounting Settings',
    'plural'   => 'Accounting Settings',




    /*
    |--------------------------------------------------------------------------
    | Fields
    |--------------------------------------------------------------------------
    */
  'sections' => [
        'general'  => 'General Settings',
        'journal'  => 'Journal Entry Settings',
        'security' => 'Security & Period Lock',
    ],
    'fields' => [
        'currency'                   => 'Currency',
        'decimal_places'             => 'Decimal Places',
        'journal_prefix'             => 'Journal Entry Prefix',
        'journal_next_number'        => 'Next Journal Number',
        'allow_backdated_entries'    => 'Allow Backdated Entries',
        'allow_future_dated_entries' => 'Allow Future Dated Entries',
        'lock_period_pwd_enabled'    => 'Enable Period Lock Password',
        'lock_period_pwd'            => 'Password',
        'vehicle_auto_post_journal_entries' => 'Auto Post Vehicle Journal Entries',
        'driver_auto_post_journal_entries'  => 'Auto Post Driver Journal Entries',
        'store_auto_post_journal_entries'   => 'Auto Post Store Journal Entries',
        'sales_auto_post_journal_entries'   => 'Auto Post Sales Journal Entries',
        'purchase_auto_post_journal_entries' => 'Auto Post Purchase Journal Entries',
    ],

    /*
    |--------------------------------------------------------------------------
    | Depreciation Methods
    |--------------------------------------------------------------------------
    */

    'depreciation' => [
        'straight_line' => 'Straight Line',
        'declining_balance' => 'Declining Balance',
    ],

    /*
    |--------------------------------------------------------------------------
    | Depreciation Frequency
    |--------------------------------------------------------------------------
    */

    'frequency' => [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Labels
    |--------------------------------------------------------------------------
    */

    'labels' => [
        'general_settings' => 'General Settings',
        'journal_settings' => 'Journal Settings',
        'depreciation_settings' => 'Depreciation Settings',
        'tax_settings' => 'Tax Settings',
        'advanced_settings' => 'Advanced Settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Misc
    |--------------------------------------------------------------------------
    */

    'unknown' => 'Unknown',
];
