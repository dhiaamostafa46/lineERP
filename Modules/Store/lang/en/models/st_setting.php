<?php
return [
    'singular' => 'Inventory Settings',
    'plural'   => 'Inventory Settings',

    'fields' => [
        'org_id' => 'Organization',
        'costing_method' => 'Costing Method',
        'allow_negative_stock' => 'Allow Negative Stock',
        'auto_calculate_cost' => 'Auto Calculate Cost',
        'stock_valuation_enabled' => 'Enable Stock Valuation',
        'auto_serial_number' => 'Auto Generate Serial Number',
        'stock_transfer_prefix' => 'Stock Transfer Prefix',
        'stocktake_prefix' => 'Stocktake Prefix',
        'inventory_account_id' => 'Inventory Account',
        'cogs_account_id' => 'COGS Account',
        'sales_account_id' => 'Sales Account',
        'track_quantity' => 'Track Quantity',
        'track_batch' => 'Track Batch',
        'track_expiry' => 'Track Expiry Date',
        'allow_backorders' => 'Allow Backorders',
        'lead_time_days' => 'Lead Time (Days)',
        'min_stock' => 'Minimum Stock',
        'max_stock' => 'Maximum Stock',
        'reorder_point' => 'Reorder Point',
        'default_transfer_type' => 'Default Transfer Type',
    ],
    'types' => [
        1 => 'Direct Transfer',
        2 => 'Indirect Transfer',
    ],
];
