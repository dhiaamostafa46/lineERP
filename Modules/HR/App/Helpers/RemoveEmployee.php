<?php

namespace Modules\HR\App\Helpers;

use App\Models\Employee;
use App\Models\User;
use Modules\HR\App\Models\HrEmployee;

trait RemoveEmployee
{
    /**
     * Check if the employee has any associated data.
     *
     * @param \Modules\HR\App\Models\HrEmployee $data_Employe
     * @return array
     */

    public function checkEmployee($data_Employe)
    {
        $messages = [];
        //($data_Employe);
        // Check for advances
        $advances = $data_Employe
            ->advances()
            ->whereYear('due_at', '>', date('Y'))
            ->orWhere(function ($query) {
                $query->whereYear('due_at', '=', date('Y'))->whereMonth('due_at', '>=', date('m'));
            })
            ->where('status', 2)
            ->where('employee_id', $data_Employe->id)
            ->exists();
        //  dd($advances);
        $emid = 6;
        // Check for penalties
        $penalties = $data_Employe
            ->penalties()
            ->whereYear('due_date', '>', date('Y'))
            ->orWhere(function ($query) {
                $query->whereYear('due_date', '=', date('Y'))->whereMonth('due_date', '>=', date('m'));
            })
            ->where('status', 2)
            ->where('employee_id', $data_Employe->id)
            ->exists();
        // dd($penalties);
        // Check for rewards
        $rewards = $data_Employe
            ->rewards()
            ->whereYear('due_date', '>', date('Y'))
            ->orWhere(function ($query) {
                $query->whereYear('due_date', '=', date('Y'))->whereMonth('due_date', '>=', date('m'));
            })
            ->where('status', 2)
            ->where('employee_id', $data_Employe->id)
            ->exists();

        // Check for tasks
        $task = $data_Employe->Task()->where('status', 2)->exists();

        // Check for custodies
        $custodies = $data_Employe
            ->Custodies()
            ->whereIn('status', [1, 2, 3])
            ->exists();

        // Check for associated data and add messages if any
        if ($advances) {
            $messages[] = __('hr::models/hr_end_service.employee_messages.has_advances');
        }

        if ($penalties) {
            $messages[] = __('hr::models/hr_end_service.employee_messages.has_penalties'); // Corrected key reference
        }

        if ($rewards) {
            $messages[] = __('hr::models/hr_end_service.employee_messages.has_rewards');
        }

        if ($custodies) {
            $messages[] = __('hr::models/hr_end_service.employee_messages.has_commitments');
        }

        if ($task) {
            $messages[] = __('hr::models/hr_end_service.employee_messages.has_tasks');
        }

        // Return messages if any associated data is found
        return count($messages) > 0 ? ['status' => false, 'messages' => $messages] : ['status' => true];
    }

    /**
     * Delete an employee and all associated data.
     *
     * @param \Modules\HR\App\Models\HrEmployee $data_Employe
     * @param bool $force
     * @return array
     */
    public function DeleteEmployee($data_Employe, $force = false)
    {
        // Check if the employee can be deleted
        if (!$force) {
            $check = $this->checkEmployee($data_Employe);

            if (!$check['status']) {
                return ['status' => false, 'messages' => [$check['messages']]];
            }
        }

        try {
            // Delete associated data
            $data_Employe->penalties()->delete();
            $data_Employe->advances()->delete();
            $data_Employe->rewards()->delete();
            $data_Employe->Custodies()->delete();
            $data_Employe->Contract()->delete();
            $data_Employe->salary()->delete();
            $data_Employe->Task()->delete();
            // $data_Employe->Place()->delete();
            $data_Employe->AbsentRequests()->delete();
            $data_Employe->Document()->delete();
            $data_Employe->holidays()->delete();
            $data_Employe->main_employee->identity()->delete();
            $data_Employe->main_employee->bank()->delete();
            $user = $data_Employe->main_employee->user;
            // Delete the main employee record
            $data_Employe->main_employee()->delete();

            // Delete the employee itself
            $data_Employe->delete();

            if ($user) {
                $user->delete();
            }

            return [
                'status' => true,
                'message' => 'تم حذف الموظف وجميع البيانات المرتبطة بنجاح.',
            ];
        } catch (\Exception $e) {
            // Handle any errors that occur during deletion
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get an employee's data for deletion.
     *
     * @param int $id
     * @return array
     */
    public function GetemployeeDelete($id)
    {
        // Retrieve the employee, including soft deleted records
        $HrEmployee = HrEmployee::where('id', $id)->onlyTrashed()->first();

        // If employee not found
        if (!$HrEmployee) {
            return ['status' => false, 'message' => 'الموظف غير موجود'];
        }

        // Gather related data for deletion, including soft deleted records
        // $data['penalties'] = $data['HrEmployee']->penalties()->withTrashed()->get();
        // $data['advances'] = $data['HrEmployee']->advances()->withTrashed()->get();
        // $data['rewards'] = $data['HrEmployee']->rewards()->withTrashed()->get();
        // $data['Custodies'] = $data['HrEmployee']->Custodies()->withTrashed()->get();
        // $data['Contract'] = $data['HrEmployee']->Contract()->withTrashed()->get();
        // $data['salary'] = $data['HrEmployee']->salary()->withTrashed()->get();
        // $data['Task'] = $data['HrEmployee']->Task()->withTrashed()->get();
        // $data['Place'] = $data['HrEmployee']->Place()->withTrashed()->get();
        // $data['AbsentRequests'] = $data['HrEmployee']->AbsentRequests()->withTrashed()->get();
        // $data['Document'] = $data['HrEmployee']->Document()->withTrashed()->get();
        // $data['holidays'] = $data['HrEmployee']->holidays()->withTrashed()->get();

        // $data['identity'] = $data['HrEmployee']->main_employee->identity()->withTrashed()->get();
        // $data['bank'] = $data['HrEmployee']->main_employee->bank()->withTrashed()->get();

        // // Load the user associated with the main_employee
        // $data['user'] = $data['HrEmployee']->main_employee->user()->withTrashed()->first();

        // Return the gathered data
        return $HrEmployee;
    }

    /**
     * Restore an employee and all associated data from the archive.
     *
     * @param int $id
     * @return array
     */
    public function RestoreEmployee($id)
    {
        $data_Employe = HrEmployee::where('id', $id)->onlyTrashed()->first();

        if (!$data_Employe) {
            return [
                'status' => false,
                'message' => 'الموظف غير موجود في الأرشيف',
            ];
        }

        try {
            // Restore the main employee record and user first if needed
            $mainEmployee = $data_Employe->main_employee()->withTrashed()->first();
            
            if ($mainEmployee) {
                if ($mainEmployee->trashed()) {
                    $mainEmployee->restore();
                }
                
                $user = $mainEmployee->user()->withTrashed()->first();
                if ($user && $user->trashed()) {
                    $user->restore();
                }

                $mainEmployee->identity()->withTrashed()->restore();
                $mainEmployee->bank()->withTrashed()->restore();
            }

            // Restore associated data
            $data_Employe->penalties()->withTrashed()->restore();
            $data_Employe->advances()->withTrashed()->restore();
            $data_Employe->rewards()->withTrashed()->restore();
            $data_Employe->Custodies()->withTrashed()->restore();
            $data_Employe->Contract()->withTrashed()->restore();
            $data_Employe->salary()->withTrashed()->restore();
            $data_Employe->Task()->withTrashed()->restore();
            $data_Employe->AbsentRequests()->withTrashed()->restore();
            $data_Employe->Document()->withTrashed()->restore();
            $data_Employe->holidays()->withTrashed()->restore();

            // Restore the employee itself
            $data_Employe->restore();

            return [
                'status' => true,
                'message' => 'تم استرجاع الموظف وجميع البيانات المرتبطة بنجاح.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء الاسترجاع: ' . $e->getMessage(),
            ];
        }
    }
}
