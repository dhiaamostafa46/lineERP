<?php

return [
    'singular' => 'Application',
    'plural' => 'Applications & Integrations',
    'title' => 'Applications',
    'hub_hub_title' => 'Central Integration Hub (Evix Hub)',
    'hub_sync_btn' => 'Sync from Hub',
    'hub_sync_desc' => 'Automatically update platforms list, schemas, and features from hub.evixdev.com',
    'sync_success' => 'Applications catalog synced successfully from Evix Hub (:count platforms available).',

    // Stats
    'stats_total' => 'Total Platforms',
    'stats_active' => 'Active Integrations',
    'stats_connected' => 'Verified & Connected',
    'stats_need_config' => 'Requires Configuration',

    // Categories
    'categories' => [
        'all' => 'All Applications',
        'ecommerce' => 'E-Commerce & Marketplaces',
        'payment_gateway' => 'Payment Gateways',
        'payments' => 'Payment Gateways',
        'shipping' => 'Shipping & Delivery',
        'delivery' => 'Shipping & Delivery',
        'government' => 'Government & Compliance',
        'messaging' => 'Messaging & SMS',
        'accounting' => 'Accounting & Invoicing',
        'accounting_tax' => 'Accounting & Tax (ZATCA)',
        'hr' => 'Human Resources & Payroll',
        'fleet_tracking' => 'Fleet & GPS Tracking',
        'internal_engine' => 'Internal Engines & Modules',
        'other' => 'Other Integrations',
    ],

    // Statuses
    'statuses' => [
        'all' => 'All Statuses',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'connected' => 'Connected',
        'disconnected' => 'Disconnected',
    ],

    // Actions & Fields
    'fields' => [
        'app_code' => 'Application Code',
        'name' => 'Platform Name',
        'category' => 'Category',
        'status' => 'Status',
        'environment' => 'Environment',
        'webhook_url' => 'Webhook URL',
    ],

    'configure' => 'Configure Integration',
    'activate' => 'Activate Platform',
    'deactivate' => 'Deactivate Application',
    'documentation' => 'Official Documentation',
    'copy_webhook' => 'Copy Webhook URL',
    'copied' => 'Copied!',
    'live_mode' => 'Live / Production',
    'sandbox_mode' => 'Sandbox / Test Mode',
    'credentials_section' => 'API Credentials & Authentication',
    'webhook_instructions' => 'Add this URL into the platform webhook settings to automatically receive events and sync orders/invoices.',
    'last_connected' => 'Last connected: :time',
    'never_connected' => 'Never connected',
];
