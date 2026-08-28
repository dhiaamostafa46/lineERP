<?php

return [

    'singular' => 'Product and Service',
    'plural'   => 'Products and Services',
    'product'  => 'Product',
    'service'  => 'Service',
    'products' => 'Products',
    'services' => 'Services',
    'sizes'    => 'Sizes',
    'fields'   => [
        'id'                => 'ID',
        'name'              => 'Product Name',
        'type'              => 'Product Type',
        'details'           => 'Details',
        'barcode'           => 'Barcode',
        'min_quantity'      => 'Minimum Quantity',
        'category_id'       => 'Category',
        'kitchen_id'        => 'Kitchen',
        'unit_id'           => 'Unit',
        'cost_price'        => 'Cost Price',
        'prod_price'        => 'Sale Price',
        'vat'               => 'VAT',
        'calories'          => 'Calories',
        'base_unit_id'      => 'Base Unit',
        'sale_unit_id'      => 'Sale Unit',
        'purchase_unit_id'  => 'Purchase Unit',
        'report_unit_id'    => 'Report Unit',
        'img'               => 'Image',
        's_from'            => 'Working From',
        's_to'              => 'Working To',
        'work_days'         => 'Working Days',
        'have_sizes'        => 'Has Sizes',
        'status'            => 'Status',
        'product'           => 'Product',
        'service'           => 'Service',
        'created_at'        => 'Created At',
        'updated_at'        => 'Updated At',
    ],

    'unit' => [
        'unit_id'           => 'Unit',
        'conversion_factor' => 'Conversion Factor',
        'is_base'           => 'Base Unit',
        'average_cost'      => 'Average Cost',
    ],

    'size' => [
        'product_id'                   => 'Product',
        'name'                         => 'Size Name',
        'sale_price'                   => 'Sale Price',
        'cost_price'                   => 'Cost Price',
        'base_unit_consumption_factor' => 'Base Unit Consumption Factor',
        'barcode'                      => 'Barcode',
        'status'                       => 'Status',
    ],

    'placeholders' => [
        'name'      => 'Enter product name',
        'details'   => 'Enter product details',
        'size_name' => 'Enter size name',
        'barcode'   => 'Enter barcode',
    ],

        'sections' => [
            'basic_info'   => 'Basic Information',
            'other_info'   => 'Other Information',
            'resturnet_info'=> 'resturnet Information',
            'vat_info'     => 'VAT Information',
            'units'        => 'Units',
            'sizes'        => 'Sizes',
            'add_unit'     => 'Add Unit',
            'add_size'     => 'Add Size',
        ],




    'import' => [
        'category_name_description' => 'Name of :field',
        'kitchen_name_description'  => 'Name of :field',
        'unit_name_description'     => 'Name of :field',
        'typs_description'          => ':field (1 = Product, 2 = Service, 3 = Sizes)',
    ],

    'import_instructions' => [
        'line1' => 'The file must be in Excel format (.xlsx or .xls).',
        'line2' => 'To ensure a successful import, please follow the column structure in the template file. You can download the template from here:',
        'line3' => 'Please do not change the order of columns or their headers to ensure success.',
        'line4' => 'Make sure that category, unit and kitchen names already exist in the system.',
        'size_hint' => 'For Sizes: Use format (Product Name - Size Name) e.g. (T-Shirt - XL)',
        'unit_mandatory_hint' => 'Unit is mandatory for all types, including services.',
    ],

    'messages' => [
        'min_one_unit' => 'At least one unit must be selected for the product.',
    ],

];
