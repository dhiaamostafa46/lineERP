<?php

return [
    'singular' => 'Task',
    'plural' => 'Tasks',
    'fields' => [
        'id' => 'ID',
        'title' => 'Title',
        'description' => 'Description',
        'done' => 'Completion Date',
        'status' => 'Status',
        'flage' => 'Type',
        'department' => 'Department',
        'employee_id' => 'Employee',
        'Group'      => 'Group',
        'file' => 'File',

        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
    ],
    'details' => [
        'singular' => 'Task Detail',
        'plural' => 'Task Details',
        'hr_task_id' => 'Task ID',
        'description' => 'Description',
        'employee_id' => 'Employee',
        'userID' => 'User ID',
        'file' => 'File',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
    ],
    'statuses' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'processed' => 'Processed',
        'closed' => 'Closed',
    ],

    'flages' => [
        'department' => 'Department',
        'employees'  => 'Employees',
        'Group'      => 'Group',
    ],
];
