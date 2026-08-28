<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Models\HrEmployee;

use Modules\HR\App\Models\HrTimeTrack;

use Modules\HR\App\Models\HrHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class ProcessEmployeeTimeTrack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 ساعة
    public $tries = 3;

    protected $startDate;
    protected $endDate;

    /**
     * Create a new job instance.
     */
    public function __construct($dateRange)
    {
        $this->startDate = $dateRange['start_date'];
        $this->endDate = $dateRange['end_date'];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("✅ بدء معالجة TimeTrack من: {$this->startDate} إلى: {$this->endDate}");

        // تحويل التواريخ إلى Carbon
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // حساب عدد الأيام
        $totalDays = $startDate->diffInDays($endDate) + 1;
        Log::info("📅 إجمالي الأيام المراد معالجتها: {$totalDays}");

        // المرور على كل يوم
        $currentDate = $startDate->copy();
        $dayCounter = 1;
        $successCount = 0;
        $failureCount = 0;

        while ($currentDate->lte($endDate)) {
            Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            try {
                // معالجة اليوم
                $this->processDayData($currentDate);
                $successCount++;
                Log::info("✅ تم معالجة اليوم بنجاح");
            } catch (\Exception $e) {
                $failureCount++;
                Log::error("❌ فشل معالجة اليوم {$currentDate->format('Y-m-d')}: " . $e->getMessage());

            }

            // الانتقال لليوم التالي
            $currentDate->addDay();
            $dayCounter++;
        }


        Log::info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        Log::info("✅ تم الانتهاء من معالجة جميع الأيام");

    }

    /**
     * معالجة بيانات يوم محدد
     */
    private function processDayData($date)
    {
        // الطريقة الصحيحة لاستدعاء Command مع الخيارات
        $exitCode = Artisan::call('attendance:record', [
            '--date' => $date->format('Y-m-d')
        ]);


        // التحقق من نتيجة التنفيذ
        if ($exitCode === 0) {
            Log::info("   ✓ تم تنفيذ الأمر بنجاح للتاريخ: {$date->format('Y-m-d')}");
        } else {
            Log::warning("   ⚠️ الأمر انتهى بكود خروج: {$exitCode}");
        }

        // الحصول على output الأمر (اختياري)
        $output = Artisan::output();
        if (!empty(trim($output))) {
            Log::info("   📝 Output: " . trim($output));
        }
    }
}
