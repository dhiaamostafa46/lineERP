<?php

return [
    'singular' => 'Official Holiday',
    'plural'   => 'Official Holidays',

    'fields'   => [
        'id'           => 'ID',
        'name'         => 'Name',
        'start_date'   => 'Start Date',
        'end_date'     => 'End Date',
        'description'  => 'Description',
        'rules'        => 'Recurrence Rules',
        'is_recurring' => 'Recurring',
        'status'       => 'Status',
        'type'         => 'Type',
        'color'        => 'Color',
        'created_at'   => 'Created At',
        'updated_at'   => 'Updated At',
    ],

    'types' => [
        'holiday' => 'Holiday',
        'event'   => 'Event',
    ],
];
