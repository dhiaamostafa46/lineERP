<?php

return [
    'singular' => 'Area',
    'plural' => 'Areas',
    'fields' => [
        'id' => '#',
        'code' => 'Code',
        'name' => 'Name',
        'status' => 'Status',
        'cities_count' => 'Cities count',
        'created_at' => 'Created at',
    ],
    'messages' => [
        'cannot_delete_has_cities' => 'Cannot delete this area because it has linked cities.',
    ],
];
