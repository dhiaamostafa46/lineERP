<?php

return [
    'singular' => 'Work Type',
    'plural' => 'Work Types',
    'static' => 'Fixed',
    'flexible' => 'Flexible',

    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'status' => 'Status',
        'type' => 'Type',
        'work_hours' => 'Work Hours',
        'from' => 'From',
        'to' => 'To',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'work_days' => 'Work Days',
        'shifts' => 'Shifts',
        'is_active' => 'Active',

        'early_entry' => 'Early Entry',
        'late_entry' => 'Late Entry',
        'early_exit' => 'Early Exit',
        'late_exit' => 'Late Exit',
        'entry_start' => 'Entry Period End',
        'exit_end' => 'Exit Period Start',
        'exempt_days' => 'Exempt Days',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'Specificperiod' => 'Specific Period',
    ],

    'sections' => [
        'attendance_settings' => 'Attendance Settings',
        'entry_period' => 'Entry Period',
        'days_configuration' => 'Days Configuration',
        'shifts' => 'Shift Times',
         'Specificperiods' => 'Specific periods',
    ],

    'hints' => [
        'early_entry' => 'Number of minutes allowed before the start of the shift (e.g., 30 minutes)',
        'late_entry' => 'Number of minutes allowed for lateness after the start of the shift (e.g., 15 minutes)',
        'early_exit' => 'Number of minutes allowed to leave before the end of the shift',
        'late_exit' => 'Number of minutes allowed to stay after the end of the shift',
        'entry_period_start' => 'Allowed minutes for entry registration',
        'entry_period_end' => 'Allowed minutes for exit registration',
        'work_days' => 'Select the official working days for the shift',
        'exempt_days' => 'Select the days employees are exempt from attendance (e.g., Friday and Saturday)',
    ],

    'placeholders' => [
        'minutes' => 'In minutes',
    ],
];
