<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrHolidayType;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrHolidayBalance;
use App\Models\Employee;

class HrHolidayBalanceRepository extends BaseRepository
{
    protected $fieldSearchable = ['employee_id', 'type_id', 'balance', 'annual_balance', 'previous_balance', 'status'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrHolidayBalance::class;
    }

    public function statuses(): array
    {
        return HrHolidayBalance::statuses();
    }

    public function FindBalance($employeeId, $typeId)
    {
        $holidayType = HrHolidayType::find($typeId);
        $employee = HrEmployee::find($employeeId);

        if (!$holidayType) {
            return [
                'success' => false,
                'message' => 'نوع الإجازة غير موجود',
                'data' => null,
            ];
        }

        $type = $holidayType->type;
        $allowed = $holidayType->off_days ?? 0;

        $annualBalance = 0;
        $previousBalance = 0;

        if ($type == HrHolidayType::TYPE_WITH_DEDUCTION && $employee) {
            $annualBalance = $employee->max_off_days ?? 0;
            $previousBalance = $employee->vacation_balance ?? 0;
        }

        // الحساب الديناميكي للأيام المستهلكة من جدول الطلبات مباشرة
        $query = \Modules\HR\App\Models\HrHoliday::where('employee_id', $employeeId)
            ->where('type_id', $typeId)
            ->where('status', \Modules\HR\App\Models\HrHoliday::STATUS_APPROVED);

        // إذا كانت إجازة غير مخصومة (مرضية وغيرها)، نحسب فقط للسنة الحالية (تصفير سنوي)
        if ($type != HrHolidayType::TYPE_WITH_DEDUCTION) {
            $query->whereYear('from_at', now()->year);
        }

        $usedBalance = $query->sum('requested_days') ?? 0;

        if ($type == HrHolidayType::TYPE_WITH_DEDUCTION) {
            $total = $annualBalance + $previousBalance - $usedBalance;
        } else {
            $total = $allowed - $usedBalance;
        }

        return [
            'success' => true,
            'message' => 'تم جلب بيانات الرصيد بنجاح',
            'data' => [
                'employee_id' => $employeeId,
                'type_id' => $typeId,
                'remaining_balance' => $annualBalance,
                'future_balance' => $previousBalance,
                'allowed' => $allowed,
                'status' => 2, // STATUS_ACTIVE
                'total_balance' => $total,
                'HolidayType' => $type,
                'type_name' => $holidayType->name,
            ],
        ];
    }

    public function FindAllBalances($employeeId)
    {
        $holidayTypes = HrHolidayType::activeOnly()->get();
        $balances = [];

        foreach ($holidayTypes as $type) {
            $result = $this->FindBalance($employeeId, $type->id);
            if ($result['success']) {
                $balances[] = $result['data'];
            }
        }

        return [
            'success' => true,
            'message' => 'تم جلب جميع الأرصدة بنجاح',
            'data' => $balances,
        ];
    }
}
