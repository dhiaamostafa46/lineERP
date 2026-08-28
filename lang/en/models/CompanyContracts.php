<?php

return [
    'singular' => 'Company contract',
    'plural' => 'Company contracts',
    'fields' => [
        'id' => '#',
        'company' => 'Company',
        'company_pricing_type' => 'Company pricing type',
        'company_pricing_value' => 'Company pricing value',
        'driver_payment_type' => 'Driver payment type',
        'driver_payment_value' => 'Driver payment value',
        'settlement_cycle' => 'Settlement cycle',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'status' => 'Status',
        'notes' => 'Notes',
        'created_at' => 'Created at',
    ],
    'placeholders' => [
        'select_company' => 'Select company',
    ],
    'company_pricing_types' => [
        'per_order' => 'Per order',
        'percentage' => 'Percentage',
        'monthly' => 'Monthly',
        'custom' => 'Custom',
    ],
    'driver_payment_types' => [
        'salary' => 'Salary',
        'per_order' => 'Per order',
        'percentage' => 'Percentage',
    ],
    'settlement_cycles' => [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
    ],
];
