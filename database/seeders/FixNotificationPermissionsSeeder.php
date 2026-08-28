<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixNotificationPermissionsSeeder extends Seeder
{
    /**
     * تصحيح أسماء الصلاحيات القديمة في إشعارات النظام
     * من الأسماء الخاطئة إلى الأسماء الصحيحة المطابقة للقائمة
     */
    public function run(): void
    {
        $map = [
            'hr.holidays.view'                  => 'hr.holidays.index',
            'hr.advances.view'                  => 'hr.advances.index',
            'hr.justifications.view'            => 'hr.justifications.index',
            'hr.view'                           => 'hr.documents.index',
            'vehicles.view'                     => 'vc.vehicles.index',
            'vehicles.maintenance_request.view' => 'vc.maintenance_requests.index',
            'invoices.view'                     => 'invoices.sales.index',
            'store.view'                        => 'store.direct_transfer.index',
            'pos.session.manage'                => 'pos.index',
            'accusoft.journal_entries.view'     => 'accusoft.JournalEntry.index',
            'assets.view'                       => 'accusoft.assets.index',
        ];

        $total = 0;
        foreach ($map as $old => $new) {
            $count = DB::table('notification_log_items')
                ->where('target_permission', $old)
                ->update(['target_permission' => $new]);

            if ($count > 0) {
                $this->command->info("✅ Updated {$count} rows: [{$old}] → [{$new}]");
                $total += $count;
            }
        }

        $this->command->info("✅ Total notifications updated: {$total}");
    }
}
