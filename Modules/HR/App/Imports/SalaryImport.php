<?php

namespace Modules\HR\App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\HR\App\Models\HrAllowance;
use Modules\HR\App\Models\HrDeduct;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Models\HrSalaryAllowance;
use Modules\HR\App\Models\HrSalaryDeduct;

class SalaryImport implements ToCollection, WithHeadingRow, WithStartRow
{
    public function startRow(): int
    {
        return 3; // تخطي أول صفين (العربي + الإنجليزي)
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $employee = HrEmployee::where('job_number', $row['job_number'] ?? null)->first();

                if (!$employee) {
                    Log::warning("لم يتم العثور على الموظف بالرقم الوظيفي: " . ($row['job_number'] ?? 'غير متوفر'));
                    continue;
                }

                // إنشاء أو تحديث الراتب الأساسي
                $salary = HrSalary::updateOrCreate(
                    ['employee_id' => $employee->id],
                    ['basic' => $row['basic_salary'] ?? 0]
                );
                // مصفوفات لتخزين البدلات والخصومات
                $allowances = [];
                $deducts = [];
                $valuear = [];
                 $valueerror = [];

                // معالجة الأعمدة الديناميكية
                foreach ($row as $header => $value) {
                    $valuear[$header ] = $value;
                    // تخطي الأعمدة غير المطلوبة
                    if (in_array($header, ['job_number', 'basic_salary']) || !is_numeric($value) ||$value == 0 ) {
                        continue;
                        //  $valueerror [$header ] =$value;
                    }

                    // تحويل اسم العمود إلى نص مقروء
                    $translatedName = ucwords(str_replace('_', ' ', $header));

                    // معالجة البدلات
                    if (str_contains(strtolower($header), 'allowance')) {
                        $allowance = HrAllowance::whereTranslation('name', $translatedName)->first();
                        if (!$allowance) {
                            $allowance = HrAllowance::create([
                                'status' => HrAllowance::STATUS_ACTIVE,
                                'en' => ['name' => $translatedName],
                                'ar' => ['name' => $translatedName]
                            ]);
                        }
                        $allowances[$allowance->id] = $value;
                    }
                    // معالجة الخصومات
                    elseif (str_contains(strtolower($header), 'deduction')) {
                        $deduct = HrDeduct::whereTranslation('name', $translatedName)->first();

                        if (!$deduct) {
                            $deduct = HrDeduct::create([
                                'status' => HrDeduct::STATUS_ACTIVE,
                                'en' => ['name' => $translatedName],
                                'ar' => ['name' => $translatedName]
                            ]);
                        }
                        $deducts[$deduct->id] = $value;
                    }
                }


              //  dd(    $valueerror);
                // حفظ البدلات
                if (!empty($allowances)) {
                    $this->createAllowances($allowances, $salary->id);
                }

                // حفظ الخصومات
                if (!empty($deducts)) {
                    $this->createDeducts($deducts, $salary->id);
                }
            }

            DB::commit();
            Log::info("تم استيراد الرواتب بنجاح");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("حدث خطأ أثناء استيراد الرواتب: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إنشاء أو تحديث البدلات
     */
    private function createAllowances($allowances, $salary_id): void
    {

       // dd($allowances);
        foreach ($allowances as $allowance_id => $amount) {
            HrSalaryAllowance::updateOrCreate(
                [
                    'allowance_id' => $allowance_id,
                    'salary_id' => $salary_id
                ],
                [
                    'amount' => $amount
                ]
            );
        }
    }

    /**
     * إنشاء أو تحديث الخصومات
     */
    private function createDeducts($deducts, $salary_id): void
    {

       
        foreach ($deducts as $deduct_id => $amount) {
            HrSalaryDeduct::updateOrCreate(
                [
                    'deduct_id' => $deduct_id,
                    'salary_id' => $salary_id
                ],
                [
                    'amount' => $amount
                ]
            );
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
