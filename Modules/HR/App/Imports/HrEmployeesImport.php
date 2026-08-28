<?php

namespace Modules\HR\App\Imports;

use App\Helpers\OptimizeImportEmployeeTrait;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrJob;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Models\HrShiftType;

class HrEmployeesImport implements SkipsOnError, SkipsOnFailure, ToModel, WithStartRow
{
    use OptimizeImportEmployeeTrait, SkipsErrors, SkipsFailures;

    private $rowNumber = 3; // يبدأ من 2 لأن أول سطر بيانات هو 3 ويتم الزيادة في بداية الدالة

    private $validationErrors = []; // Renamed from $errors to avoid conflict with SkipsErrors trait

    private $failedRows = [];

    public function __construct() {}

    public function startRow(): int
    {
        return 3;
    }

    /**
     * Get all validation errors after import
     */
    public function getErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Get failed rows for Excel export
     */
    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function model(array $row)
    {
        $this->rowNumber++;

        // Map columns correctly according to Excel structure
        $fullName = trim($row[0] ?? '');
        $username = trim($row[1] ?? '');
        $phone = trim($row[2] ?? '');
        $emailCompany = trim($row[3] ?? '');
        $emailPerson = trim($row[4] ?? '');
        $numberJob = trim($row[5] ?? '');
        $departmentId = trim($row[6] ?? '');
        $jobId = trim($row[7] ?? '');
        $shiftId = trim($row[8] ?? '');

        $dateOfBirth = $row[9] ?? '';
        $joiningDate = $row[10] ?? '';
        $ibn = trim($row[11] ?? '');
        $branchId = trim($row[12] ?? '');
        $address = trim($row[13] ?? '');
        $nationalAddress = trim($row[14] ?? '');
        $religion = trim($row[15] ?? '');
        $nationality = trim($row[16] ?? '');
        $maxOffDays = trim($row[17] ?? '');
        $vacationBalance = trim($row[18] ?? '');
        $maxAdvance = trim($row[19] ?? '');
        $jobLevel = trim($row[20] ?? '');
        $specialty = trim($row[21] ?? '');
        $gender = trim($row[22] ?? '');
        $maritalStatus = trim($row[23] ?? '');
        $idIqama = trim($row[24] ?? '');
        $iqamaExpiredAt = $row[25] ?? '';
        $insurance = trim($row[26] ?? '');
        $insuranceDate = $row[27] ?? '';
        $licenseExpiredAt = $row[28] ?? '';

        // ====== VALIDATION STARTS HERE ======

        // 1. Validate required fields
        if (! $departmentId || ! $jobId || ! $fullName || ! $username || ! $phone || ! $emailCompany || ! $numberJob) {
            $this->addError('الحقول المطلوبة ناقصة', [
                'الاسم الكامل' => $fullName ?: 'مفقود',
                'ملخص الاسم' => $username ?: 'مفقود',
                'رقم الجوال' => $phone ?: 'مفقود',
                'الايميل' => $emailCompany ?: 'مفقود',
                'الرقم الوظيفي' => $numberJob ?: 'مفقود',
                'القسم' => $departmentId ?: 'مفقود',
                'الوظيفة' => $jobId ?: 'مفقود',
            ], $row);

            return null;
        }

        // 2. Check for duplicate phone number FIRST
        $existingUserByPhone = User::where('phone', $phone)->first();
        if ($existingUserByPhone) {
            $this->addError('رقم الجوال مكرر', [
                'الموظف الحالي' => $fullName,
                'رقم الجوال' => $phone,
                'موجود لدى' => $existingUserByPhone->name,
                'البريد الإلكتروني للموظف الموجود' => $existingUserByPhone->email,
            ], $row);

            return null;
        }

        // 3. Check for duplicate email
        $existingUserByEmail = User::where('email', $emailCompany)->first();
        if ($existingUserByEmail) {
            $this->addError('البريد الإلكتروني مكرر', [
                'الموظف الحالي' => $fullName,
                'البريد الإلكتروني' => $emailCompany,
                'موجود لدى' => $existingUserByEmail->name,
            ], $row);

            return null;
        }

        // 4. Check for duplicate job number
        $existingUserByJobNumber = User::where('job_number', $numberJob)->first();
        if ($existingUserByJobNumber) {
            $this->addError('الرقم الوظيفي مكرر', [
                'الموظف الحالي' => $fullName,
                'الرقم الوظيفي' => $numberJob,
                'موجود لدى' => $existingUserByJobNumber->name,
            ], $row);

            return null;
        }

        // 5. Validate company email format
        if (! filter_var($emailCompany, FILTER_VALIDATE_EMAIL)) {
            $this->addError('صيغة البريد الإلكتروني للشركة غير صحيحة', [
                'الموظف' => $fullName,
                'البريد المدخل' => $emailCompany,
            ], $row);

            return null;
        }

        // 6. Validate personal email format (if provided)
        if (! empty($emailPerson) && ! filter_var($emailPerson, FILTER_VALIDATE_EMAIL)) {
            $this->addError('صيغة البريد الإلكتروني الشخصي غير صحيحة', [
                'الموظف' => $fullName,
                'البريد المدخل' => $emailPerson,
            ], $row);

            return null;
        }

        // 7. Validate phone format
        if (! preg_match("/^(05\d{8}|9665\d{8})$/", $phone)) {
            $this->addError('صيغة رقم الجوال غير صحيحة (يجب أن يكون: 05XXXXXXXX أو 9665XXXXXXXX)', [
                'الموظف' => $fullName,
                'رقم الجوال المدخل' => $phone,
            ], $row);

            return null;
        }

        if (! empty($ibn) && ! preg_match("/^SA\d{22}$/", $ibn)) {
            $this->addError('صيغة الآيبان غير صحيحة (يجب أن يكون: SA + 22 رقم)', [
                'الموظف' => $fullName,
                'الآيبان المدخل' => $ibn,
            ], $row);

            return null;
        }

        // 9. Validate Saudi ID/Iqama
        if (! empty($idIqama) && ! preg_match("/^[12]\d{9}$/", $idIqama)) {
            $this->addError('صيغة رقم الهوية/الإقامة غير صحيحة (يجب أن يكون: 10 أرقام تبدأ بـ 1 أو 2)', [
                'الموظف' => $fullName,
                'رقم الهوية المدخل' => $idIqama,
            ], $row);

            return null;
        }

        // 10. Validate numeric fields
        $maxAdvance = $maxAdvance !== '' ? $maxAdvance : 0;
        $maxOffDays = $maxOffDays !== '' ? $maxOffDays : 0;
        $vacationBalance = $vacationBalance !== '' ? $vacationBalance : 0;

        if (! is_numeric($maxAdvance) || $maxAdvance < 0) {
            $this->addError('قيمة الحد الأقصى للسلف غير صحيحة', [
                'الموظف' => $fullName,
                'القيمة المدخلة' => $maxAdvance,
            ], $row);

            return null;
        }

        if (! is_numeric($maxOffDays) || $maxOffDays < 0) {
            $this->addError('قيمة الحد الأقصى لأيام الإجازة غير صحيحة', [
                'الموظف' => $fullName,
                'القيمة المدخلة' => $maxOffDays,
            ], $row);

            return null;
        }

        if (! is_numeric($vacationBalance) || $vacationBalance < 0) {
            $this->addError('قيمة رصيد الإجازات غير صحيحة', [
                'الموظف' => $fullName,
                'القيمة المدخلة' => $vacationBalance,
            ], $row);

            return null;
        }

        // 11. Validate department, job, shift, and branch existence
        $department = $this->DepartmentFund($departmentId);
        $job = $this->JobFund($jobId);
        $shift = $this->ShiftFund($shiftId);
        $branch = $this->branchFund($branchId);

        if (! $department) {
            $this->addError('القسم غير موجود ولم يتم إنشاؤه', [
                'الموظف' => $fullName,
                'اسم القسم' => $departmentId,
            ], $row);

            return null;
        }

        if (! $job) {
            $this->addError('الوظيفة غير موجودة ولم يتم إنشاؤها', [
                'الموظف' => $fullName,
                'اسم الوظيفة' => $jobId,
            ], $row);

            return null;
        }

        // 12. Validate and convert dates
        $birthDate = $this->convertAndValidateDate($dateOfBirth);
        $joiningDateConverted = $this->convertAndValidateDate($joiningDate);
        $iqamaExpiredAtConverted = $this->convertAndValidateDate($iqamaExpiredAt);
        $insuranceDateConverted = $this->convertAndValidateDate($insuranceDate);
        $licenseExpiredAtConverted = $this->convertAndValidateDate($licenseExpiredAt);

        // 13. Validate date logic
        if ($birthDate && $joiningDateConverted) {
            $birthTimestamp = strtotime($birthDate);
            $joiningTimestamp = strtotime($joiningDateConverted);
            $currentTimestamp = time();

            if ($birthTimestamp >= $joiningTimestamp) {
                $this->addError('تاريخ الميلاد يجب أن يكون قبل تاريخ المباشرة', [
                    'الموظف' => $fullName,
                    'تاريخ الميلاد' => $birthDate,
                    'تاريخ المباشرة' => $joiningDateConverted,
                ], $row);

                return null;
            }

            if ($birthTimestamp > $currentTimestamp) {
                $this->addError('تاريخ الميلاد لا يمكن أن يكون في المستقبل', [
                    'الموظف' => $fullName,
                    'تاريخ الميلاد' => $birthDate,
                ], $row);

                return null;
            }

            $ageAtJoining = ($joiningTimestamp - $birthTimestamp) / (365.25 * 24 * 60 * 60);
            if ($ageAtJoining < 18) {
                $this->addError('عمر الموظف يجب أن يكون 18 سنة على الأقل عند تاريخ المباشرة', [
                    'الموظف' => $fullName,
                    'تاريخ الميلاد' => $birthDate,
                    'تاريخ المباشرة' => $joiningDateConverted,
                    'العمر عند المباشرة' => round($ageAtJoining, 1).' سنة',
                ], $row);

                return null;
            }
        }

        // 14. Map gender and marital status
        $genderMapped = $this->mapGender($gender);
        $maritalStatusMapped = $this->mapMaritalStatus($maritalStatus);

        if ($genderMapped === null && ! empty($gender)) {
            $this->addError('قيمة الجنس غير صحيحة', [
                'الموظف' => $fullName,
                'القيمة المدخلة' => $gender,
                'القيم المقبولة' => 'ذكر، أنثى، male، female',
            ], $row);

            return null;
        }

        // ====== VALIDATION PASSED - PROCEED WITH DATA CREATION ======

        DB::beginTransaction();
        try {
            // Create User
            $main_User = User::create([
                'name' => $fullName,
                'phone' => $phone,
                'email' => $emailCompany,
                'password' => Hash::make('Evix20'),
                'job_number' => $numberJob,
                'emp_flage' => 2,
            ]);
            $main_User->assignRole('موظف');

            // Create Employee
            $main_employee = Employee::create([
                'user_id' => $main_User->id,
                'full_name' => $fullName,
                'username' => $username,
                'phone' => $phone,
                'email' => $emailPerson,
                'address' => $address,
                'national_address' => $nationalAddress,
                'religion' => $religion,
                'nationality' => $nationality,
                'gender' => $genderMapped,
                'marital_status' => $maritalStatusMapped,
                'dob' => $birthDate,
                'branch_id' => $branch,
            ]);

            // Create HrEmployee
            $employee = HrEmployee::create([
                'employee_id' => $main_employee->id,
                'user_id' => $main_User->id,
                'max_off_days' => $maxOffDays,
                'vacation_balance' => $vacationBalance,
                'max_advance' => $maxAdvance,
                'job_level' => $jobLevel,
                'specialty' => $specialty,
                'username' => $username,
                'department_id' => $department,
                'job_id' => $job,
                'shift_id' => $shift,
                'start_at' => $joiningDateConverted,
                'job_number' => $numberJob,
                'license_expired_at' => $licenseExpiredAtConverted,
                'Direct_manager' => null,
            ]);

            // Create HrSalary
            HrSalary::create([
                'employee_id' => $employee->id,
                'basic' => config('statusSystem.minimum_basic_salary') ?? 0,
            ]);

            // Create Identity
            $main_employee->identity()->create([
                'employee_id' => $main_employee->id,
                'identity_type' => 1,
                'identity_no' => $idIqama,
                'identity_expired_at' => $iqamaExpiredAtConverted,
                'insurance_no' => $insurance,
                'insurance_expired_at' => $insuranceDateConverted,
            ]);

            // Create Bank
            $main_employee->bank()->create([
                'employee_id' => $main_employee->id,
                'iban' => $ibn,
            ]);

            // Create holiday balance
            $balanceData = [
                'max_off_days' => $maxOffDays,
                'vacation_balance' => $vacationBalance,
                'employee_id' => $main_employee->id,
            ];

            Log::info("✅ الصف {$this->rowNumber}: تم استيراد الموظف بنجاح: {$fullName}");

            DB::commit();

            return $employee;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('فشل في إنشاء الموظف', [
                'الموظف' => $fullName,
                'الخطأ' => $e->getMessage(),
            ], $row);
            Log::error("❌ الصف {$this->rowNumber}: فشل في استيراد الموظف: {$fullName}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Add error to the validation errors array with Arabic formatting
     */
    private function addError(string $message, array $context = [], array $rowData = []): void
    {
        $errorDetails = "الصف {$this->rowNumber}: {$message}";
        $detailedMessage = $message;

        if (! empty($context)) {
            $errorDetails .= "\n";
            foreach ($context as $key => $value) {
                $errorDetails .= "  • {$key}: {$value}\n";
                $detailedMessage .= " | {$key}: {$value}"; // إضافة التفاصيل للرسالة التي ستظهر في Excel
            }
        }

        $this->validationErrors[] = [
            'row' => $this->rowNumber,
            'message' => $message,
            'details' => $context,
            'formatted' => $errorDetails,
        ];

        if (! empty($rowData)) {
            // Add error message to row data for export
            $rowData['error_message'] = $detailedMessage;
            $this->failedRows[] = $rowData;
        }
        Log::warning('⚠️ '.$errorDetails);
    }

    /**
     * Find or create department by name
     */
    private function DepartmentFund(?string $departmentName): ?int
    {
        if (empty($departmentName)) {
            return null;
        }

        $department = HrDepartment::whereHas('translations', function ($query) use ($departmentName) {
            $query->where('name', $departmentName);
        })->first();

        if ($department) {
            return $department->id;
        }

        try {
            $code = Str::slug($departmentName, '-');

            $newDepartment = HrDepartment::create([
                'status' => HrDepartment::STATUS_ACTIVE,
                'code' => strtoupper($code),
                'type' => HrDepartment::TYPE_DEPARTMENT,
                'parent_id' => null,
                'owner_id' => null,
            ]);

            foreach (config('langs') as $locale => $language) {
                $newDepartment->translateOrNew($locale)->name = $departmentName;
            }

            $newDepartment->save();

            Log::info("✅ تم إنشاء قسم جديد: {$departmentName}");

            return $newDepartment->id;
        } catch (\Exception $e) {
            Log::error("❌ فشل في إنشاء القسم: {$departmentName}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Find or create job by name
     */
    private function JobFund(?string $jobName): ?int
    {
        if (empty($jobName)) {
            return null;
        }

        $job = HrJob::whereHas('translations', function ($query) use ($jobName) {
            $query->where('name', $jobName);
        })->first();

        if ($job) {
            return $job->id;
        }

        try {
            $newJob = HrJob::create([
                'status' => HrJob::STATUS_ACTIVE,
                'license_required' => HrJob::LICENSE_REQUIRED_NO,
            ]);

            foreach (config('langs') as $locale => $language) {
                $newJob->translateOrNew($locale)->name = $jobName;
            }

            $newJob->save();

            Log::info("✅ تم إنشاء وظيفة جديدة: {$jobName}");

            return $newJob->id;
        } catch (\Exception $e) {
            Log::error("❌ فشل في إنشاء الوظيفة: {$jobName}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Find or create shift by name
     */
    private function ShiftFund(?string $shiftName): ?int
    {
        if (empty($shiftName)) {
            return null;
        }

        $shift = HrShiftType::whereHas('translations', function ($query) use ($shiftName) {
            $query->where('name', $shiftName);
        })->first();

        if ($shift) {
            return $shift->id;
        }

        try {
            $newShift = HrShiftType::create([
                'status' => HrShiftType::STATUS_ACTIVE,
                'type' => HrShiftType::TYPE_STATIC,
                'work_hours' => 8,
                'early_entry' => 0,
                'late_entry' => 0,
                'early_exit' => 0,
                'late_exit' => 0,
                'entry_start' => '08:00',
                'exit_end' => '16:00',
                'exempt_days' => [],
            ]);

            foreach (config('langs') as $locale => $language) {
                $newShift->translateOrNew($locale)->name = $shiftName;
            }

            $newShift->save();

            Log::info("✅ تم إنشاء دوام جديد: {$shiftName}");

            return $newShift->id;
        } catch (\Exception $e) {
            Log::error("❌ فشل في إنشاء الدوام: {$shiftName}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Find or create branch by name
     */
    private function branchFund(?string $branchName): ?int
    {
        if (empty($branchName)) {
            return null;
        }

        $branch = Branch::whereHas('translations', function ($query) use ($branchName) {
            $query->where('name', $branchName);
        })->first();

        if ($branch) {
            return $branch->id;
        }

        try {
            $newBranch = Branch::create([
                'status' => Branch::STATUS_ACTIVE,
                'user_id' => auth()->id() ?? 1,
                'phone' => '0000000000',
                'area' => '',
                'city' => '',
                'district' => '',
                'long' => null,
                'lat' => null,
                'distance' => 0,
                'manager' => '',
                'description' => '',
            ]);

            foreach (config('langs') as $locale => $language) {
                $newBranch->translateOrNew($locale)->name = $branchName;
                $newBranch->translateOrNew($locale)->address = $branchName;
            }

            $newBranch->save();

            Log::info("✅ تم إنشاء فرع جديد: {$branchName}");

            return $newBranch->id;
        } catch (\Exception $e) {
            Log::error("❌ فشل في إنشاء الفرع: {$branchName}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Map gender string to integer
     */
    private function mapGender(?string $gender): ?int
    {
        return match (strtolower(trim($gender ?? ''))) {
            'male', 'ذكر' => 1,
            'female', 'أنثى', 'انثى' => 2,
            default => null,
        };
    }

    /**
     * Map marital status string to integer
     */
    private function mapMaritalStatus(?string $status): ?int
    {
        return match (strtolower(trim($status ?? ''))) {
            'single', 'اعزب', 'عازب', 'عازبة' => 1,
            'married', 'متزوج', 'متزوجة' => 2,
            'divorced', 'مطلق', 'مطلقة' => 3,
            'widowed', 'ارمل', 'أرمل', 'أرملة', 'ارملة' => 4,
            'engaged', 'مخطوب', 'مخطوبة' => 5,
            default => null,
        };
    }

    /**
     * Convert and validate date from various formats
     */
    private function convertAndValidateDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (is_numeric($date)) {
            return $this->convertExcelSerialToDate($date);
        }

        $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
        if ($dateTime && $dateTime->format('d/m/Y') === $date) {
            return $dateTime->format('Y-m-d');
        }

        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        if ($dateTime && $dateTime->format('Y-m-d') === $date) {
            return $date;
        }

        $dateTime = \DateTime::createFromFormat('m/d/Y', $date);
        if ($dateTime && $dateTime->format('m/d/Y') === $date) {
            return $dateTime->format('Y-m-d');
        }

        Log::warning("لم يتم التعرف على صيغة التاريخ: {$date}");

        return null;
    }

    /**
     * Convert Excel serial number to date
     */
    private function convertExcelSerialToDate($serial): ?string
    {
        try {
            $unixTime = ($serial - 25569) * 86400;
            $dateTime = \DateTime::createFromFormat('U', (string) $unixTime);

            return $dateTime ? $dateTime->format('Y-m-d') : null;
        } catch (\Exception $e) {
            Log::error("فشل تحويل التاريخ من Excel: {$serial}", ['error' => $e->getMessage()]);

            return null;
        }
    }
}