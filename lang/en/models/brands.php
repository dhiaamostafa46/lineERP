<?php

return [
    'singular' => 'Vehicles Brand',
    'plural'   => 'Vehicle Brand',
    'fields'   => [
        'id'          => '#',
        'name'        => 'Brand Name',
        'file'     => 'Logo',
        'status'      => 'Brand',
        'delmsg'      => ' cannot be deleted because there are different models or vehicles. You can change the status to inactive.','لا يمكن حذف الشركة لوجود موديلات او مركبات مختلفة يمكنك تغير الحالة الى غير نشط',
        'created_at'  => 'Created At', 
        'updated_at'  => 'Updated At',
    ],
];
