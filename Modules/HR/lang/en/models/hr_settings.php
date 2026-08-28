<?php

return [
    'singular' => 'Human Resources Settings',
    'plural'   => 'Human Resources Settings',

    'sections' => [
        'payroll_settings'    => 'Payroll Settings',
        'attendance_settings' => 'Attendance & Fingerprint Settings',
        'leave_settings'      => 'Leave Settings',
    ],

    'fields'   => [
        'id'                             => '#',
        'delivery_payroll_at'            => 'Payroll Delivery Day',
        'preparing_payroll_at'           => 'Payroll Preparation Day',
        'min_salary'                     => 'Minimum Salary',
        'max_off_days'                   => 'Maximum Leave Days',
        'currency'                       => 'Currency',
        'created_at'                     => 'Created At',
        'updated_at'                     => 'Updated At',
        'approval_payroll'               => 'Payroll Approvals',
        'user_id'                        => 'User',
        'sort'                           => 'Approval Order',
        'is_current'                     => 'Payroll Creator',
        'missing_fingerprint_policy'     => 'Missing Fingerprint Policy',
        'calculate_missing_fingerprint'  => 'Calculate Missing Fingerprint',
        'leave_include_weekend'          => 'Include Weekends in Leave',
        'leave_include_holidays'         => 'Include Official Holidays in Leave',
    ],

    'missing_fp' => [
        'half_day'   => 'Deduct Half Day',
        'full_day'   => 'Deduct Full Day',
        'half_shift' => 'Deduct Half Shift',
        'full_shift' => 'Deduct Full Shift',
        'ignore'     => 'Ignore',
    ],

    'hints' => [
        'delivery_payroll_hint'      => 'Day of the month when salaries are delivered to employees',
        'preparing_payroll_hint'     => 'Day of the month when payroll preparation starts',
        'min_salary_hint'            => 'Minimum salary allowed in the system',
        'max_off_days_hint'          => 'Maximum number of leave days allowed per year',
        'calculate_fingerprint_hint' => 'When enabled, deductions will be applied for missing fingerprints',
        'missing_fingerprint_hint'   => 'Select the policy applied when a fingerprint is missing',
        'leave_weekend_hint'         => 'When enabled, weekends will be counted as part of the leave',
        'leave_holidays_hint'        => 'When enabled, official holidays will be counted as part of the leave',
    ],

    'placeholders' => [
        'enter_day'     => 'Enter day (1–31)',
        'enter_amount'  => 'Enter amount',
        'enter_days'    => 'Enter number of days',
        'select_user'   => 'Select user',
        'select_policy' => 'Select policy',
    ],

    'buttons' => [
        'add_approver' => 'Add New Approver',
    ],

    'messages' => [
        'confirm_delete'        => 'Are you sure you want to delete this item?',
        'at_least_one_approver' => 'At least one approver is required',
        'settings_updated'      => 'Settings have been updated successfully',
        'one_current_required'  => 'At least one payroll creator must be selected',
    ],
];
