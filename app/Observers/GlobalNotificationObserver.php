<?php

namespace App\Observers;

use App\Models\AccuSoft\JournalEntry;
use App\Models\NotificationItem;
use App\Services\NotificationService;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrJustification;
use Modules\Pos\App\Models\PosDeviceSession;
use Modules\Pos\App\Models\PosSession;
use Modules\Store\App\Models\StTransfer;

class GlobalNotificationObserver
{
    /**
     * Handle created events across audited models
     */
    public function created($model): void
    {
        // 1. Stock Transfer Request
        if ($model instanceof StTransfer) {
            NotificationService::sendToPermission(
                'store.direct_transfer.index',
                NotificationItem::TYPE_STOCK_TRANSFER_PENDING,
                "📦 طلب نقل مخزني جديد (#{$model->transfer_number})",
                "تم إنشاء طلب نقل بين المستودعات برقم #{$model->transfer_number}. يتطلب المراجعة والاستلام.",
                $model,
                NotificationItem::PRIORITY_NORMAL
            );
        }

        // 2. Journal Entry Created (Pending Approval only — STATUS_PENDING = 4)
        if ($model instanceof JournalEntry) {
            if ($model->status === JournalEntry::STATUS_PENDING) {
                NotificationService::sendToPermission(
                    'accusoft.JournalEntry.index',
                    NotificationItem::TYPE_UNPOSTED_JOURNAL_ENTRY,
                    "📊 قيد يومية معلق يتطلب الاعتماد (#{$model->entry_number})",
                    "تم إنشاء قيد يومية برقم #{$model->entry_number} بقيمة إجمالية {$model->total_debit} ريال وهو معلق في انتظار اعتمادك وترحيله.",
                    $model,
                    NotificationItem::PRIORITY_NORMAL
                );
            }
        }
    }

    /**
     * Handle updated events across audited models
     */
    public function updated($model): void
    {
        // 1. HR Holiday Status Update
        if ($model instanceof HrHoliday && $model->wasChanged('status')) {
            $employeeUserId = $model->employee->main_employee->user->id ?? null;
            if ($employeeUserId) {
                $statusText = $model->status_text ?? 'تحديث الحالة';
                NotificationService::sendToUser(
                    $employeeUserId,
                    NotificationItem::TYPE_REQUEST_STATUS,
                    '🔔 تحديث حالة طلب الإجازة الخاص بك',
                    "تم تحديث حالة طلب الإجازة المقدم بتاريخ {$model->from_at} إلى: ({$statusText}).",
                    $model,
                    NotificationItem::PRIORITY_HIGH
                );
            }
        }

        // 2. HR Advance Status Update
        if ($model instanceof HrAdvance && $model->wasChanged('status')) {
            $employeeUserId = $model->employee->main_employee->user->id ?? null;
            if ($employeeUserId) {
                $statusText = $model->status_text ?? 'تحديث الحالة';
                NotificationService::sendToUser(
                    $employeeUserId,
                    NotificationItem::TYPE_REQUEST_STATUS,
                    '🔔 تحديث حالة طلب السلفة الخاض بك',
                    "تم تحديث حالة طلب السلفة بقيمة {$model->amount} ريال إلى: ({$statusText}).",
                    $model,
                    NotificationItem::PRIORITY_HIGH
                );
            }
        }

        // 3. POS Session Closed with Cash Discrepancy
        if (($model instanceof PosDeviceSession || $model instanceof PosSession) && $model->wasChanged('status')) {
            if (isset($model->difference) && abs($model->difference) > 0) {
                NotificationService::sendUrgent(
                    NotificationItem::TYPE_POS_CASH_DISCREPANCY,
                    "🚨 عجز/تباين في صندوق جلسة نقطة البيع (#{$model->id})",
                    "تم إغلاق جلسة POS رقم #{$model->id} بوجود تباين في الصندوق بمقدار {$model->difference} ريال.",
                    $model,
                    null,
                    'pos.session.manage',
                    ['difference' => $model->difference]
                );
            }
        }
    }
}
