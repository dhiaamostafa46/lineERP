<?php

return [
    'singular' => 'Fixed Asset Category',
    'plural' => 'Fixed Asset Categories',
    'fields' => [
        'name' => 'Category Name',
        'asset_account_id' => 'Asset Account',
        'accumulated_depreciation_account_id' => 'Accumulated Depreciation Account',
        'depreciation_expense_account_id' => 'Depreciation Expense Account',
        'default_depreciation_method' => 'Default Depreciation Method',
        'default_useful_life' => 'Default Useful Life (Years)',
        'has_accounting_effect' => 'Has Accounting Effect (Generate Entries)',
        'calculation_type' => 'Calculation Type',
        'useful_life_type' => 'Useful Life Type',
        'created_at' => 'Created At',
    ],
    'methods' => [
        'none' => 'None',
        'straight_line' => 'Straight Line',
        'declining_balance' => 'Declining Balance',
        'sum_of_years' => 'Sum of Years',
        'units_of_production' => 'Units of Production',
    ],
];
