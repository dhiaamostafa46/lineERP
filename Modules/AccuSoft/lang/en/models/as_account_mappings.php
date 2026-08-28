<?php

return [
    'singular' => 'Account Mapping',
    'plural'   => 'Account Mappings',
    'fields'   => [
        'id'            => 'ID',
        'mapping_key'   => 'Mapping Key',
        'account_id'    => 'Linked Account',
        'entity_type'   => 'Linked Entity Type',
        'entity_id'     => 'Linked Entity ID',
        'name'          => 'Name',
        'status'        => 'Status',
        'settings'      => 'Settings',
        'created_at'    => 'Created At',
        'updated_at'    => 'Updated At',
        'deleted_at'    => 'Deleted At',
        'status_label'  => 'Activation Status',
        'status_help'   => '1 = Inactive, 2 = Active',
        'entity_label'  => 'Optional Entity Link',
        'entity_help'   => 'This mapping can be linked to a specific entity such as a product, customer, or branch',
        'settings_label'=> 'Additional Settings',
        'settings_help' => 'Customizable JSON information according to system requirements',
    ],
];
