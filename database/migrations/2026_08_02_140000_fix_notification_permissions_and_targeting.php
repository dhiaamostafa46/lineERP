<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations on live database.
     */
    public function up(): void
    {
        // 1. تحويل الإشعارات الموجهة برقم المستخدم إلى الصلاحيات المناسبة لمديري الأقسام
        $targetingMap = [
            'leave_request' => 'hr.holidays.index',
            'advance_request' => 'hr.advances.index',
            'settlement_request' => 'hr.justifications.index',
            'iqama_expiry' => 'hr.documents.index',
            'insurance_expiry' => 'hr.documents.index',
            'passport_expiry' => 'hr.documents.index',
            'driver_license_expiry' => 'hr.documents.index',
            'vehicle_license_expiry' => 'vc.vehicles.index',
            'traffic_violation' => 'vc.vehicles.index',
            'low_stock' => 'store.stores.index',
        ];

        foreach ($targetingMap as $type => $permission) {
            DB::table('notification_log_items')
                ->where('notification_type', $type)
                ->whereNotNull('user_id')
                ->whereNull('target_permission')
                ->update([
                    'target_permission' => $permission,
                    'user_id' => null,
                ]);
        }

        // 2. تصحيح أسماء الصلاحيات القديمة الخاطئة إن وجدت
        $permissionNameMap = [
            'hr.holidays.view' => 'hr.holidays.index',
            'hr.advances.view' => 'hr.advances.index',
            'hr.justifications.view' => 'hr.justifications.index',
            'hr.view' => 'hr.documents.index',
            'vehicles.view' => 'vc.vehicles.index',
            'vehicles.maintenance_request.view' => 'vc.maintenance_requests.index',
            'invoices.view' => 'invoices.sales.index',
            'store.view' => 'store.direct_transfer.index',
            'pos.session.manage' => 'pos.index',
            'accusoft.journal_entries.view' => 'accusoft.JournalEntry.index',
            'assets.view' => 'accusoft.assets.index',
        ];

        foreach ($permissionNameMap as $old => $new) {
            DB::table('notification_log_items')
                ->where('target_permission', $old)
                ->update(['target_permission' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed for record correction migration
    }
};
