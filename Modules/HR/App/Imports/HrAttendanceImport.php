<?php

namespace Modules\HR\App\Imports;

use App\Models\Employee;
use Modules\HR\App\Models\HrSalary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Models\HrEmployee;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Collection;
use Modules\HR\App\Models\HrAttendance;
use Carbon\Carbon;

class HrAttendanceImport implements ToCollection, WithStartRow, WithChunkReading, WithBatchInserts
{
    private $employeeCache = [];
    private $shiftCache = [];
    private $attendanceBuffer = [];
    private const BUFFER_SIZE = 500;

    // لتخزين تواريخ البداية والنهاية
    private $startDate = null;
    private $endDate = null;

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000; // معالجة 1000 صف في المرة
    }

    public function batchSize(): int
    {
        return 500;
    }

    /**
     * الحصول على تاريخ البداية والنهاية
     */
    public function getDateRange()
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    /**
     * معالجة البيانات على دفعات
     */
    public function collection(Collection $rows)
    {
        // تجميع أرقام الموظفين لجلبها دفعة واحدة

        $jobNumbers = $rows->pluck(0)->unique()->filter();

        $this->preloadEmployees($jobNumbers);

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $this->processRow($row);
            }

            // حفظ ما تبقى في الـ buffer
            $this->flushBuffer();

            DB::commit();
            Log::info('✅ تم استيراد ' . $rows->count() . ' سجل حضور بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ خطأ في استيراد الحضور: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * معالجة صف واحد
     */
    private function processRow($row)
    {
        $jobNumber = $row[0];
        $r = $this->splitDateTime($row[1]);

        $date = $r['date'];
        $time = $r['time'];

        // $date = $this->convertAndValidateDate($row[1]);
        // $time = $this->convertAndValidateTime($row[2]);

        if (!$jobNumber || !$date || !$time) {
            Log::warning('⚠️ بيانات غير صالحة: ' . json_encode($row));
            return;
        }

        // تحديث تاريخ البداية والنهاية
        if ($this->startDate === null || $date < $this->startDate) {
            $this->startDate = $date;
        }
        if ($this->endDate === null || $date > $this->endDate) {
            $this->endDate = $date;
        }

        $employee = $this->getEmployeeFromCache($jobNumber);

        if (!$employee) {
            Log::warning("⚠️ موظف غير موجود: {$jobNumber}");
            return;
        }

        $shiftDetails = $this->calculateShiftDetails($employee, $time);

        $this->attendanceBuffer[] = [
            'employee_id' => $employee->id,
            'name' => null,
            'lat' => null,
            'lon' => null,
            'places_id' => null,
            'address' => null,
            'type' => $shiftDetails['from'] == null ? 2 : 1,
            'check_time' => $time,
            'early_arrival' => $shiftDetails['early_arrival'] ?? 0,
            'delay' => $shiftDetails['lateArrival'] ?? 0,
            'early_leave' => $shiftDetails['earlyLeave'] ?? 0,
            'overtime' => $shiftDetails['overtime'] ?? 0,
            'shift_from' => $shiftDetails['from'],
            'shift_to' => $shiftDetails['to'],
            'date' => $date,
            'Active' => 1,
            'distance' => 0,
            'kind' => 1, // سيتم تحديثه في flushBuffer
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // حفظ الدفعة عند امتلاء الـ buffer
        if (count($this->attendanceBuffer) >= self::BUFFER_SIZE) {
            $this->flushBuffer();
        }
    }

    /**
     * حفظ البيانات المتجمعة في قاعدة البيانات
     */
    private function flushBuffer()
    {
        if (empty($this->attendanceBuffer)) {
            return;
        }

        // تجميع السجلات حسب (employee_id, date, shift_from, shift_to) لتحديد kind
        $groupedRecords = collect($this->attendanceBuffer)->groupBy(function ($item) {
            return $item['employee_id'] . '|' . $item['date'] . '|' . ($item['shift_from'] ?? 'null') . '|' . ($item['shift_to'] ?? 'null');
        });

        $finalRecords = [];

        foreach ($groupedRecords as $key => $records) {
            // ترتيب السجلات حسب الوقت
            $sortedRecords = collect($records)->sortBy('check_time')->values();

            // تحديث kind: أول سجل = حضور (1)، الباقي = انصراف (2)
            foreach ($sortedRecords as $index => $record) {
                $record['kind'] = $index === 0 ? 1 : 2;
                $finalRecords[] = $record;
            }
        }



        // إدراج جميع السجلات (بدون تجميع)
        if (!empty($finalRecords)) {
            HrAttendance::insert($finalRecords);
            Log::info('✅ تم حفظ ' . count($finalRecords) . ' سجل حضور');
        }

        // تفريغ الـ buffer
        $this->attendanceBuffer = [];
    }

    /**
     * تحميل الموظفين مسبقاً في الذاكرة
     */
    private function preloadEmployees($jobNumbers)
    {
        $employees = HrEmployee::with('shift.shifts')->whereIn('job_number', $jobNumbers)->get()->keyBy('job_number');

        foreach ($employees as $jobNumber => $employee) {
            $this->employeeCache[$jobNumber] = $employee;
            $this->shiftCache[$employee->id] = $employee->shift;
        }
    }

    /**
     * الحصول على الموظف من الذاكرة المؤقتة
     */
    private function getEmployeeFromCache($jobNumber)
    {
        return $this->employeeCache[$jobNumber] ?? null;
    }

    /**
     * حساب تفاصيل الوردية
     */
    private function calculateShiftDetails($employee, $time)
    {
        $shifts = $this->shiftCache[$employee->id]->shifts ?? [];

        if (empty($shifts)) {
            return [
                'early_arrival' => null,
                'lateArrival' => null,
                'earlyLeave' => null,
                'overtime' => null,
                'from' => null,
                'to' => null,
            ];
        }

        $currentTime = Carbon::parse($time);
        $currentTimeInSeconds = $currentTime->secondsSinceMidnight();

        $closestShift = null;
        $smallestDiff = PHP_INT_MAX;

        foreach ($shifts as $shift) {
            $fromTimeInSeconds = Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
            $toTimeInSeconds = Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

            $diffFromStart = abs($currentTimeInSeconds - $fromTimeInSeconds);
            $diffFromEnd = abs($currentTimeInSeconds - $toTimeInSeconds);
            $minDiff = min($diffFromStart, $diffFromEnd);

            if ($minDiff < $smallestDiff) {
                $smallestDiff = $minDiff;
                $closestShift = $shift;
            }
        }

        if (!$closestShift) {
            return [
                'early_arrival' => null,
                'lateArrival' => null,
                'earlyLeave' => null,
                'overtime' => null,
                'from' => null,
                'to' => null,
            ];
        }

        $fromSeconds = Carbon::createFromFormat('H:i:s', $closestShift->from)->secondsSinceMidnight();
        $toSeconds = Carbon::createFromFormat('H:i:s', $closestShift->to)->secondsSinceMidnight();

        $early_arrival = null;
        $lateArrival = null;
        $earlyLeave = null;
        $overtime = null;

        if ($currentTimeInSeconds <= $fromSeconds) {
            $early_arrival = $fromSeconds - $currentTimeInSeconds;
        } else {
            $lateArrival = $currentTimeInSeconds - $fromSeconds;
        }

        if ($currentTimeInSeconds <= $toSeconds) {
            $earlyLeave = $toSeconds - $currentTimeInSeconds;
        } else {
            $overtime = $currentTimeInSeconds - $toSeconds;
        }

        return [
            'early_arrival' => $early_arrival,
            'lateArrival' => $lateArrival,
            'earlyLeave' => $earlyLeave,
            'overtime' => $overtime,
            'from' => $closestShift->from,
            'to' => $closestShift->to,
        ];
    }

    private function convertAndValidateDate($date): ?string
    {
        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        // 1) إذا كان رقمياً — نتعامل معه كـ Excel serial date (مثال: 44600)
        if (is_numeric($date)) {
            // Excel epoch -> Unix seconds: (excel_date - 25569) * 86400
            // نأخذ floor لضمان integer seconds عند ضبط الطابع الزمني
            $unixTime = (float) $date - 25569.0;
            $unixSeconds = (int) floor($unixTime * 86400.0);

            try {
                $dt = \DateTime::createFromFormat('U', (string) $unixSeconds);
                return $dt->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // 2) جرب عدة صيغ شائعة للنصوص
        $formats = [
            'Y-m-d', // 2025-10-23
            'd/m/Y', // 23/10/2025
            'd-m-Y', // 23-10-2025
            'm/d/Y', // 10/23/2025
            'Y/m/d', // 2025/10/23
        ];

        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $date);
            if ($dt !== false) {
                // التأكد من المطابقة الحرفية (handle leading zeros, etc.)
                if ($dt->format($fmt) === $date) {
                    return $dt->format('Y-m-d');
                }
            }
        }

        // 3) تجربة createFromFormat مع اكتشاف أخطاء إن وُجدت (fallback)
        $dtFallback = date_create($date);
        if ($dtFallback !== false) {
            return $dtFallback->format('Y-m-d');
        }

        return null;
    }

    private function convertAndValidateTime($time): ?string
    {
        if (is_numeric($time)) {
            $unixTimestamp = $time * 86400;
            $timeOnly = Carbon::createFromTimestamp($unixTimestamp)->setTimezone('UTC');
            return $timeOnly ? $timeOnly->format('H:i:s') : null;
        }

        $dateTime = \DateTime::createFromFormat('H:i:s', $time);
        if ($dateTime) {
            return $dateTime->format('H:i:s');
        }

        return null;
    }

    private function splitDateTime($value): array
    {
        $value = trim((string) $value);

        // إذا كان رقمياً = Excel serial يدمج التاريخ والوقت معاً
        if (is_numeric($value)) {
            // Excel date contains both date & time
            $unix = ((float) $value - 25569.0) * 86400.0;

            $dt = Carbon::createFromTimestamp($unix)->setTimezone('UTC');

            return [
                'date' => $dt->format('Y-m-d'),
                'time' => $dt->format('H:i:s'),
            ];
        }

        // إذا كان نصياً: حاول فصله إلى تاريخ + وقت
        // مثال: "11/2/2025 9:29:38 AM"
        $parts = preg_split('/\s+/', $value);

        if (count($parts) >= 2) {
            return [
                'date' => $this->convertAndValidateDate($parts[0]),
                'time' => $this->convertAndValidateTime($parts[1] . (isset($parts[2]) ? ' ' . $parts[2] : '')),
            ];
        }

        return [
            'date' => null,
            'time' => null,
        ];
    }
}
