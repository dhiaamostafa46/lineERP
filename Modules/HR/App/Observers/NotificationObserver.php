<?php

namespace Modules\HR\App\Observers;

use App\Models\NotificationItem;
use App\Services\NotificationService;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrJustification;

class NotificationObserver
{
    /**
     * Handle model created event
     */
    public function created($model): void
    {
        $employee = $model->employee;
        $employeeName = $employee->name ?? ($employee->main_employee->full_name ?? ($employee->main_employee->name ?? ($employee->user->name ?? '')));

        if ($model instanceof HrHoliday) {
            NotificationService::sendToPermission(
                'hr.holidays.index',
                NotificationItem::TYPE_LEAVE_REQUEST,
                "🌴 طلب إجازة جديد للموظف ({$employeeName})",
                "تم تقديم طلب إجازة جديد للفترة من {$model->from_at} إلى {$model->end_at}.",
                $model,
                NotificationItem::PRIORITY_HIGH,
                [
                    'date' => ($model->created_at ?? now())->format('Y-m-d H:i A'),
                    'body' => $model->comments ?? '',
                    'Holiday' => ($model->from_at ?? '').' : '.($model->end_at ?? ''),
                ]
            );
        } elseif ($model instanceof HrAdvance) {
            NotificationService::sendToPermission(
                'hr.advances.index',
                NotificationItem::TYPE_ADVANCE_REQUEST,
                "💰 طلب سلفة جديد للموظف ({$employeeName})",
                "تم تقديم طلب سلفة بقيمة {$model->amount} ريال. الملاحظات: {$model->description}",
                $model,
                NotificationItem::PRIORITY_HIGH,
                [
                    'date' => ($model->created_at ?? now())->format('Y-m-d H:i A'),
                    'body' => $model->description ?? '',
                    'Advance' => $model->amount,
                ]
            );
        } elseif ($model instanceof HrJustification) {
            $shiftData = '';
            if ($model->HrShift) {
                $shiftData = ($model->HrShift->from ?? '').' : '.
                            ($model->HrShift->to ?? '').' :: '.
                            ($model->type_text ?? '');
            }

            NotificationService::sendToPermission(
                'hr.justifications.index',
                NotificationItem::TYPE_SETTLEMENT_REQUEST,
                "📝 طلب تسوية جديد للموظف ({$employeeName})",
                "تم تقديم طلب تسوية دوام جديد. السبب: {$model->reason}",
                $model,
                NotificationItem::PRIORITY_NORMAL,
                [
                    'date' => ($model->created_at ?? now())->format('Y-m-d H:i A'),
                    'body' => $model->reason ?? '',
                    'Justification' => $shiftData,
                ]
            );
        }
    }

    /**
     * Handle model deleted event
     */
    public function deleted($model): void
    {
        $notification = NotificationItem::where('notifiable_type', get_class($model))
            ->where('notifiable_id', $model->id)
            ->where('status', NotificationItem::STATUS_PENDING)
            ->first();

        if ($notification) {
            $notification->update([
                'status' => NotificationItem::STATUS_CANCELLED,
                'confirmed_at' => now(),
                'read_at' => now(),
            ]);
        }
    }
}
