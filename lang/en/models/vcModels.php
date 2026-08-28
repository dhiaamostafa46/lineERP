<?php

return [
    'singular' => 'Vehicle Model',
    'plural'   => 'Vehicle Models',
    'fields'   => [
        'id'          => '#',
        'name'        => 'Model Name',
        'file'     => 'Logo',
        'status'      => 'Status',
         'delmsg'      =>'Cannot delete the model because there are associated vehicles. You can change the status to inactive.',
        'created_at'  => 'Created At',
        'updated_at'  => 'Updated At',
    ],
];
