<?php

return [
    'singular' => 'Reward',
    'plural'   => 'Rewards',
    'fields'   => [
        // Added automatically
        'created' => 'Created',

        'id'                    => 'Id',
        'employee_id'           => 'Employee',
        'type'                  => 'Type',
        'amount'                => 'Amount',
        'status'                => 'Status',
        'over_time'             => 'Over Time',
        'days_off'              => 'Days Off',
        'start_at'              => 'Start At',
        'end_at'                => 'End At',
        'created_at'            => 'Created At',
        'updated_at'            => 'Updated At',
        'overtime'              => 'Overtime',
        'static_amount'         => 'Static Amount',
        'compensatory_holidays' => 'Compensatory Holidays',
        'in_kind_reward'        => 'In Kind Reward',
        'note'                  => 'Note',
        'value'                 => 'Value',
    ],
    'days' => ':days_off days from :start_at to :end_at',
    'over_time_hours' => ':over_time hours equal :amount',

    // Added automatically group
    'alerts' => [
        'created' => 'Created',
    ],
];

