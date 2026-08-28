<?php

return [
    'singular' => 'Attendance',
    'plural' => 'Attendance',
    'presence' => 'Presence',
    'absence' => 'Absence',
    'checkout' => 'Checkout',
    'location' => 'Location',
    'attendance_actions' => 'Attendance Actions',

    'total_days' => 'Total Days',
    'not_found' => 'Location not found', // Added text
    'attendance_success' => 'Attendance recorded successfully', // Added text
    'checkout_success' => 'Checkout recorded successfully', // Added text
    'location_far' => 'Location is far', // Added text
    'attendance_movement' => 'Attendance Movement',
    'outactual_work_hours' => ' work done beyond official shift time', // work done beyond official shift time
    'outactual_work_hours' => 'You cannot record movement now, you are outside official working hours',
    'entry_time_ended' => 'Entry time has ended',
     'exit_time_not_started_yet' => 'Exit time has not started yet',
    'exit_time_not_started_yet' => 'Exit time has not started yet',


    'Attendance_table' => [
        'employee_name' => 'Employee Name',
        'attendance_date' => 'Attendance Date',
        'first_record' => 'First Record',
        'last_record' => 'Last Record',
        'work_hours' => 'Work Hours',
        'actual_work_hours' => 'Actual Work Hours', // إضافة حقل لساعات العمل الفعلية
        'work_period' => 'Work Period', // إضافة حقل فترة الدوام
        'location' => 'Location',
        'late' => 'Late',
        'departure' => 'Early Departure',
        'overtime' => 'Overtime',
        'earlyArrival' => 'Early Arrival',
        'movement' => 'Movements',
        'total' => 'Total',
        'apply' => 'Apply', // Adding the term 'Apply'
        'ignore' => 'Ignore',
        'Balanced' => 'Balanced',
    ],

    'fields' => [
        'id' => 'ID',
        'employee_id' => 'Employee',
        'employee_name' => 'Employee Name',  // Added: Employee Name
        'job_title' => 'Job Title',         // Added: Job Title
        'department' => 'Department',          // Added: Department
        'total_days' => 'Total Days',      // Added: Total Days (again for clarity in fields)
        'day' => 'Day',
        'name' => 'Name',
        'lat' => 'Latitude',
        'lon' => 'Longitude',
        'address' => 'Address',
        'status' => 'Status',
        'distance' => 'Distance',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
        'date' => 'Date',
        'earlyArrival' => 'Early Arrival',            // Added: Date
        'check_in_time' => 'Check-in Time',      // Added: Check-in Time
        'check_out_time' => 'Check-out Time',   // Added: Check-out Time
        'delay' => 'Delay',                  // Added: Delay
        'early_leave' => 'Early Leave',    // Added: Early Leave
        'work_hours' => 'Work Hours',
    ],

    'weekdays' => [
        'sunday' => 'Sunday',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
    ],

    'type' => [
        'presence' => 'Presence',
        'checkout' => 'Checkout',
    ],
];
