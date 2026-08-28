<?php
return [
    'singular' => 'Attendance and Time Tracking Policy',
    'plural' => 'Attendance and Time Tracking Policies',
    'rules_settings' => 'Policy Rules Settings',

    'types' => [
        'absence' => 'Absence',
        'late' => 'Late or Early Exit',
        'overtime' => 'Overtime',
    ],

    'calculation_types' => [
        'day' => 'Per Day',
        'hour' => 'Per Hour',
    ],

    'scopes' => [
        'employee' => 'Employees',
        'department' => 'Departments',
        'job' => 'Positions',
        'branch' => 'Branches',
    ],

    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'is_automatic' => 'Automatic',
        'scope' => 'Application Scope',
        'salary_effect' => 'Policy Impact on Salary',
        'scope_ids' => 'Covered Items',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'status' => 'Status',
        'type' => 'Type',
        'settings' => 'Settings',
        'calculation_type' => 'Calculation Type',
        'covered' => 'Covered',
        'id' => 'ID',
        'entity_name' => 'Name',
    ],

    'saudi_penalties' => [
        'title' => 'Deductions for Late Arrival and Early Exit',
        'delay_info_1' => 'Deductions are applied for days when the employee is late or leaves early.',
        'delay_info_2' => 'Deductions are calculated as a percentage of salary using the approved scale: (5%, 10%, 15%, 50%, 100%, ...).',
        'delay_info_3' => 'Deductions can be based on the official daily wage or hourly wage according to company policy.',

        'absence_title' => 'Deductions for Unexcused Absence',
        'absence_info_1' => 'Deductions apply for days when the employee is absent without a valid excuse.',
        'absence_info_2' => 'Absences are not considered within the official attendance records unless proven unexcused.',
        'absence_info_3' => 'Absence deduction is based only on the official daily wage.',

        'early_exit_title' => 'Deductions for Early Exit',
        'early_exit_info_1' => 'Early exit, whether authorized or unauthorized, is calculated based on the official daily wage.',
        'early_exit_info_2' => 'The number of days for which the deduction applies is determined per early exit incident.',

        'time_range' => 'Time Range',
        'daily_deduction' => 'Daily Deduction',
        'deduction' => 'Deduction',
        'hourly_deduction' => 'Hourly Deduction',
        'allowed_days' => 'Allowed Days',
        'allowed_absence' => 'Permitted Absence',
        'offense_suffix' => 'Offense',

        'violations' => [
            'late_15' => 'Late less than 15 minutes',
            'late_15_30' => 'Late 15 to 30 minutes',
            'late_30_60' => 'Late 30 to 60 minutes',
            'late_60_plus' => 'Late more than 60 minutes',
            'early_exit_15' => 'Early exit less than 15 minutes',
            'early_exit_15_plus' => 'Early exit more than 15 minutes',
            'absence' => 'Unexcused Absence',
        ],

        'recurrence' => [
            'first' => 'First Offense',
            'second' => 'Second Offense',
            'third' => 'Third Offense',
            'fourth' => 'Fourth Offense',
        ],

        'actions' => [
            'warning' => 'Written Warning',
            'deduction_percentage' => 'Deduct Percentage of Daily Wage',
            'deduction_days' => 'Deduct Days',
            'suspension' => 'Suspension',
            'dismissal' => 'Termination',
        ],
    ],

    'rules' => [
        'first_offense' => 'First Offense',
        'second_offense' => 'Second Offense',
        'third_offense' => 'Third Offense',
        'fourth_offense' => 'Fourth Offense',
        'overtime_rate' => 'Overtime Rate',
    ],
];
