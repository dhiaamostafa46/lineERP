<?php

return [
    'singular' => 'Notification',
    'plural' => 'Notifications',
    'alerts' => 'Alerts',
    'reports' => 'Reports',
    'total' => 'Total',
    'unread' => 'Unread',
    'all' => 'All',
    'edit' => 'Edit',
    'view_all' => 'View All',
    'no_notifications' => 'No notifications available',
    'no_unread_notifications' => 'No unread notifications',
    'all_caught_up' => 'You are all caught up',
    'mark_read' => 'Mark as Read',
    'mark_all_read' => 'Mark All as Read',
    'all_marked_as_read' => 'All notifications marked as read successfully',
    'notification_marked_read' => 'Notification marked as read',
    'all_notifications_marked_read' => 'All notifications marked as read',
    'notification_deleted' => 'Notification deleted',
    'read_notifications_cleared' => 'All read notifications cleared',
    'notification_not_found' => 'Notification not found',
    'error_occurred' => 'An error occurred, please try again',

    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent 🚨',
    ],

    'modules' => [
        'hr' => 'Human Resources',
        'vehicles' => 'Vehicles & Fleet',
        'invoices' => 'Invoices & Sales',
        'store' => 'Inventory & Stores',
        'pos' => 'Point of Sale (POS)',
        'accounting' => 'Accounting & Finance',
        'assets' => 'Fixed Assets',
        'system' => 'System & General',
    ],

    'fields' => [
        'id' => '#',
        'org_id' => 'Organization ID',
        'notification_type' => 'Notification Type',
        'notifiable_id' => 'Recipient / Employee',
        'notifiable_type' => 'Notifiable Type',
        'channel' => 'Channel',
        'status' => 'Status',
        'priority' => 'Priority',
        'fingerprint' => 'Fingerprint',
        'read_at' => 'Read At',
        'extra' => 'Additional Data',
        'anonymous_notifiable_properties' => 'Anonymous Notifiable Properties',
        'confirmed_at' => 'Confirmed At',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    'info' => [
        'date' => 'Date',
        'body' => 'Notes',
        'Advance' => 'Advance Amount',
        'Holiday' => 'Leave',
        'Justification' => 'Settlement',
        'expiry_date' => 'Expiry Date',
        'license_expiry_date' => 'Registration Expiry',
        'license_expiration_date' => 'License Expiry',
        'current_stock' => 'Current Stock',
        'min_quantity' => 'Safety Threshold',
        'amount' => 'Amount',
    ],

    'status' => [
        'pending' => 'Pending',
        'read' => 'Read',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ],

    'channel' => [
        'database' => 'System',
        'email' => 'Email',
        'sms' => 'SMS',
        'mobile_push' => 'Mobile App',
    ],

    'type' => [
        // HR
        'iqama_expiry' => 'Iqama Expiry',
        'insurance_expiry' => 'Insurance Expiry',
        'passport_expiry' => 'Passport Expiry',
        'leave_request' => 'Leave Request',
        'advance_request' => 'Advance Request',
        'settlement_request' => 'Settlement Request',
        'request_status' => 'Request Status Update',

        // Vehicles
        'vehicle_license_expiry' => 'Vehicle Registration Expiry',
        'driver_license_expiry' => 'Driver License Expiry',
        'maintenance_request' => 'Vehicle Maintenance Request',
        'traffic_violation' => 'Traffic Violation',

        // Invoices
        'quotation_expired' => 'Quotation Expired',
        'invoice_due' => 'Overdue Invoice',
        'purchase_return_pending' => 'Pending Purchase Return',

        // Store
        'low_stock' => 'Low Stock Alert',
        'stock_transfer_pending' => 'Pending Stock Transfer',

        // POS
        'pos_session_open' => 'Open POS Session',
        'pos_cash_discrepancy' => 'POS Cash Discrepancy',

        // Accounting
        'unposted_journal_entry' => 'Unposted Journal Entry',

        // Assets
        'asset_maintenance' => 'Asset Maintenance Alert',
        'asset_depreciation' => 'Asset Depreciation Alert',

        // System
        'system_alert' => 'General System Alert',
    ],
];
