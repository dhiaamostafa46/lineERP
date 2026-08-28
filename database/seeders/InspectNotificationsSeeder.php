<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectNotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $items = DB::table('notification_log_items')
            ->select('id', 'notification_type', 'user_id', 'target_permission', 'target_role', 'notifiable_id', 'status')
            ->orderBy('id', 'desc')
            ->limit(60)
            ->get();

        $this->command->line(sprintf("%-4s | %-35s | %-7s | %-35s | %-10s | %-12s | %s",
            'ID', 'notification_type', 'user_id', 'target_permission', 'notif_id', 'status', 'role'));
        $this->command->line(str_repeat('-', 130));

        foreach ($items as $item) {
            $this->command->line(sprintf("%-4s | %-35s | %-7s | %-35s | %-10s | %-12s | %s",
                $item->id,
                $item->notification_type,
                $item->user_id ?? 'NULL',
                $item->target_permission ?? 'NULL',
                $item->notifiable_id ?? 'NULL',
                $item->status,
                $item->target_role ?? 'NULL'
            ));
        }
    }
}
