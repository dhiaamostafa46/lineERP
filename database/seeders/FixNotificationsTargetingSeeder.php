<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixNotificationsTargetingSeeder extends Seeder
{
    /**
     * تحويل الإشعارات القديمة من user_id إلى target_permission
     * حتى تظهر لجميع المسؤولين بدلاً من الموظف نفسه فقط
     */
    public function run(): void
    {
        // خريطة: نوع الإشعار → الصلاحية المطلوبة
        $typePermissionMap = [
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

        $total = 0;
        foreach ($typePermissionMap as $type => $permission) {
            // نحوّل فقط الإشعارات التي لها user_id (موجهة لشخص بعينه)
            // ونجعلها موجهة بالصلاحية بدلاً من ذلك
            $count = DB::table('notification_log_items')
                ->where('notification_type', $type)
                ->whereNotNull('user_id')
                ->whereNull('target_permission')
                ->update([
                    'target_permission' => $permission,
                    'user_id' => null,
                ]);

            if ($count > 0) {
                $this->command->info("✅ Fixed {$count} records of type [{$type}] → permission [{$permission}]");
                $total += $count;
            }
        }

        $this->command->info("✅ Total notifications fixed: {$total}");
    }
}
