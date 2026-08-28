<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Helpers\TrackerTrait;
use Modules\HR\App\Models\HrHolidayType;
use Modules\HR\App\Models\HrHolidayBalance;

class HrHolidayRepository extends BaseRepository
{
    use TrackerTrait;

    protected $fieldSearchable = ['employee_id', 'status', 'type_id', 'from_at', 'end_at'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrHoliday::class;
    }

    public function paginate(int $perPage, array $columns = ['*']): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model()::orderBy('id', 'desc')->paginate($perPage, $columns);
    }

    // Status
    public function statuses()
    {
        return $this->model()::statuses();
    }

    // types
    public function types()
    {
        return HrHolidayType::get()->pluck('name', 'id')->toArray();
    }

    // employees
    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    public function checkTracking($reward): void
    {
        $this->setTracker($reward, $reward->employee_id, HrTracker::TYPE_HOLIDAYS);
    }

    //     public function balance($employee_id, $type_id)
    //     {
    //         $holidayType = HrHolidayType::find($type_id);
    //         if (!$holidayType) {
    //             return 0;
    //         }

    //         $holidayBalance = HrHolidayBalance::where('employee_id', $employee_id)->where('type_id', $type_id)->first();
    //         $setting = hr_setting();

    //         $shift = [];
    //         if ($setting->leave_include_weekend) {
    //             $shift = $holidayBalance->employee->shift->work_days;
    //         }
    //   dd(   $setting);
    //         if (!$holidayBalance) {
    //             return [
    //                 'success' => true,
    //                 'balance' => 0,
    //                 'annual_balance' => 0,
    //                 'allowed' => 0,
    //                 'shift' => [],
    //             ];
    //         }

    //         return [
    //             'success' => true,
    //             'balance' => $holidayBalance->balance ?? 0,
    //             'annual_balance' => $holidayBalance->annual_balance + $holidayBalance->previous_balance,
    //             'allowed' => $holidayBalance->allowed,
    //             'shift' => $shift,
    //         ];
    //     }

    public function balance($employee_id, $type_id)
    {
        $balanceRepo = app(\Modules\HR\App\Repositories\HrHolidayBalanceRepository::class);
        $result = $balanceRepo->FindBalance($employee_id, $type_id);

        if (!$result['success']) {
            return [
                'success' => false,
                'message' => 'Holiday type not found',
            ];
        }

        $setting = hr_setting();
        $employee = HrEmployee::find($employee_id);
        
        $shift = optional(optional($employee)->shift)->work_days ?? [];

        // احتساب أيام العمل فقط إذا الإجازة تشمل العطلة الأسبوعية
        if ($setting && $setting->leave_include_weekend) {
            $shift = [];
        }

        $data = $result['data'];
        
        // Fetch used balance from db since the original code expected 'balance' to be USED balance.
        // Or wait, my FindBalance returns:
        // 'remaining_balance' => annual_balance
        // 'future_balance' => previous_balance
        // 'total_balance' => (annual + previous) - used
        // we can deduce used balance: (annual + previous) - total
        $totalAnnual = $data['remaining_balance'] + $data['future_balance'];
        $usedBalance = $totalAnnual - $data['total_balance'];

        // Wait, what if it's a non-deductible type? Then total is 0 + 0 - used, so used = -total
        $holidayBalanceRecord = HrHolidayBalance::where('employee_id', $employee_id)->where('type_id', $type_id)->first();
        $realUsedBalance = $holidayBalanceRecord->balance ?? 0;

        return [
            'success' => true,
            'balance' => $realUsedBalance,
            'annual_balance' => $totalAnnual,
            'allowed' => $data['allowed'],
            'shift' => $shift,
            'total_remaining' => $data['total_balance'] // Adding this just in case frontend needs it
        ];
    }
}
