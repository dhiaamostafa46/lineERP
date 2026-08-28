<?php
// resources/lang/en/hr_holidays.php

return [
    'singular' => 'Holiday',
    'plural' => 'Holidays',
    'fields' => [
        'id' => 'ID',
        'employee_id' => 'Employee',
        'status' => 'Status',
        'approver' => 'Approver',
        'type_id' => 'Holiday Type',
        'from_at' => 'From Date',
        'end_at' => 'To Date',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'details' => 'Details',
        'add' => 'Add Holiday Request',
        'allowed' => 'Allowed Balance',
        'required_days' => 'Requested Days',
        'remaining_balance' => 'Remaining Balance',
        'annual_balance' => 'Annual Balance',
        'used_balance' => 'Used Balance',
        'future_balance' => 'Future Balance',
        'attachment' => 'Attachments',
        'comments' => 'Comments',
    ],
    'sections' => [
        'employee_information' => 'Employee Information',
        'holiday_details' => 'Holiday Details',
        'dates' => 'Dates',
        'system_information' => 'System Information',
           'attachment' => 'Attachments',
    ],
    'messages' => [
        'not_found' => 'Record not found',
        'created' => 'Holiday created successfully',
        'updated' => 'Holiday updated successfully',
        'deleted' => 'Holiday deleted successfully',
    ],
];
