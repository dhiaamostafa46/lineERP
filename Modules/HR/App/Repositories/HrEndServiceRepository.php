<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Helpers\RemoveEmployee;
use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrEndService;

class HrEndServiceRepository extends BaseRepository
{
    use RemoveEmployee;
    protected $fieldSearchable = ['employee_id', 'end', 'description', 'reason', 'reward_amount', 'approved', 'status'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrEndService::class;
    }

    public function statuses(): array
    {
        return HrEndService::statuses();
    }
    public function EmployessSalary($data)
    {
        // البحث عن الموظف
        $emp = HrEmployee::find($data->employee_id);

        // تحقق من وجود الموظف
        if (!$emp) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'not found',
                ],
                404,
            );
        }

        // الحصول على الراتب الأساسي
        $salary = $emp->salary->basic ?? 0;

        // إعداد المصفوفة للعودة
        $array = [
            'start' => $emp->start_at,
            'salary' => $salary,
        ];

        // إرجاع البيانات كاستجابة JSON
        return response()->json([
            'success' => true,
            'data' => $array,
        ]);
    }

    public function reasons(): array
    {
        return HrEndService::reasons();
    }

    public function ChechEmployeeData($id)
    {
        $emp = HrEmployee::findOrFail($id);
        return $this->checkEmployee($emp);
    }

    public function RemoveEmpData($id)
    {
        $emp = HrEmployee::findOrFail($id);
        return $this->DeleteEmployee($emp, true);
    }



    public function employees($current_id = null): array
    {
        $query = HrEmployee::with('main_employee:id,username');
        if ($current_id) {
            $query->withTrashed()->where(function($q) use ($current_id) {
                $q->whereNull('deleted_at')->orWhere('id', $current_id);
            });
        }
        return $query->get()->pluck('username', 'id')->toArray();
    }
}
