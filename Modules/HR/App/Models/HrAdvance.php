<?php

namespace Modules\HR\App\Models;

use App\Helpers\ImageUploaderTrait;
use App\Models\User;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HrAdvance extends Model
{
    use SoftDeletes, ImageUploaderTrait;

    public $table = 'hr_advances';

    // Status Constants
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    protected $fieldSearchable = [
        'employee_id',
        'approver_id',
        'payroll_id',
        'description',
        'due_at',
        'status',
        'amount',
        'from_date',
        'to_date',
        'attachment',
        'reason',
        'journal_entry_id',
    ];

    public $fillable = [
        'employee_id',
        'approver_id',
        'payroll_id',
        'description',
        'due_at',
        'status',
        'amount',
        'from_date',
        'to_date',
        'attachment',
        'reason',
        'journal_entry_id',
    ];

    protected $casts = [
        'due_at' => 'date',
        'from_date' => 'date',
        'to_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * تعيين الملف المرفق
     */
    public function setAttachmentAttribute($file)
    {
        try {
            // إذا كان الملف نص (يعني اسم الملف القديم) نحتفظ به
            if (is_string($file)) {
                $this->attributes['attachment'] = $file;
                return;
            }

            // إذا كان ملف جديد
            if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                // حذف الملف القديم إذا كان موجوداً
                if (isset($this->attributes['attachment']) && $this->attributes['attachment']) {
                    $this->deleteAttachmentFile($this->attributes['attachment']);
                }

                // إنشاء اسم الملف الجديد
                $fileName = $this->createFileName($file);



                // حفظ الملف
                $this->saveAttachmentFile($file, $fileName);

                // حفظ اسم الملف في قاعدة البيانات
                $this->attributes['attachment'] = $fileName;
            }
        } catch (\Throwable $th) {

            $this->attributes['attachment'] = null;
        }
    }


       protected static function boot()
    {
        parent::boot();

        static::deleting(function ($advance) {
            if ($advance->attachment) {
                $advance->deleteAttachmentFile($advance->attachment);
            }
        });
    }



    /**
     * الحصول على مسار الملف الكامل
     */
    public function getAttachmentPathAttribute()
    {
        if ($this->attachment && File::exists(public_path('uploads/images/Advances/' . $this->attachment))) {
            return 'uploads/images/Advances/' . $this->attachment;
        }
        return null;
    }

    /**
     * الحصول على رابط الملف
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_path ? asset($this->attachment_path) : null;
    }





    /**
     * قواعد التحقق
     */
    public static array $rules = [
        'employee_id' => 'required|exists:hr_employees,id',
        'amount' => 'required|numeric|min:0',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'status' => 'nullable|in:1,2,3',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        'reason' => 'nullable|string|max:255',
    ];

    /**
     * حالات السلفة
     */
    public static function statuses()
    {
        return [
            self::STATUS_PENDING => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected'),
        ];
    }

    /**
     * الحصول على نص الحالة
     */
    public function getStatusTextAttribute()
    {

        
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    /**
     * الحصول على شارة الحالة
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_REJECTED => 'badge badge-danger',
            self::STATUS_APPROVED => 'badge badge-success',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    // ==================== Relations ====================

    /**
     * علاقة الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    /**
     * علاقة المعتمد
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * علاقة الراتب
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id')
                    ->where('status', '<', HrPayroll::STATUS_ACCREDITED);
    }

    /**
     * علاقة الأقساط الشهرية
     */
    public function monthlyPayments()
    {
        return $this->hasMany(HrMonthlyPayment::class, 'hr_advance_id');
    }

    // ==================== Scopes ====================

    /**
     * فقط السلف المرتبطة بكشف راتب
     */
    public function scopeInPayroll($query)
    {
        return $query->whereNotNull('payroll_id');
    }

    /**
     * فقط السلف غير المرتبطة بكشف راتب
     */
    public function scopeOutPayroll($query)
    {
        return $query->whereNull('payroll_id');
    }

    /**
     * فقط السلف المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * فقط السلف المعتمدة
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * فقط السلف المرفوضة
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // ==================== Business Logic Methods ====================

    /**
     * حساب إجمالي السلف المعتمدة للموظف
     *
     * @param int $employeeId
     * @return float
     */
    public static function getTotalApprovedAdvances($employeeId)
    {
        return self::where('employee_id', $employeeId)
            ->where('status', self::STATUS_APPROVED)
            ->sum('amount');
    }

    /**
     * حساب إجمالي المبالغ المدفوعة من الأقساط الشهرية للموظف
     *
     * @param int $employeeId
     * @return float
     */
   public static function getTotalPaidInstallments($employeeId)
    {
        return HrMonthlyPayment::whereHas('advance', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
        ->where('type', HrMonthlyPayment::TYPE_REPAID)
        ->sum('amount');
    }




    /**
     * حساب الرصيد المتبقي (الدين) على الموظف من السلف المعتمدة
     * الرصيد = إجمالي السلف المعتمدة - إجمالي المبالغ المدفوعة
     *
     * @param int $employeeId
     * @return float
     */
    public static function getEmployeeAdvanceBalance($employeeId)
    {
        $totalApproved = self::getTotalApprovedAdvances($employeeId);
        $totalPaid = self::getTotalPaidInstallments($employeeId);

        return $totalApproved - $totalPaid;
    }

    /**
     * الحصول على تفاصيل رصيد السلف للموظف
     *
     * @param int $employeeId
     * @return array
     */
    public static function getEmployeeAdvanceDetails($employeeId)
    {
        $totalApproved = self::getTotalApprovedAdvances($employeeId);
        $totalPaid = self::getTotalPaidInstallments($employeeId);
        $balance = $totalApproved - $totalPaid;


        return [
            'total_approved' => $totalApproved,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'payment_progress' => $totalApproved > 0 ? ($totalPaid / $totalApproved) * 100 : 0,
        ];
    }

    /**
     * الحصول على تفاصيل السلف للموظف حسب الحالة
     *
     * @param int $employeeId
     * @param int|null $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEmployeeAdvancesByStatus($employeeId, $status = null)
    {
        $query = self::where('employee_id', $employeeId)
            ->with('monthlyPayments');

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * التحقق من إمكانية الموظف الحصول على سلفة جديدة
     *
     * @param int $employeeId
     * @param float $requestedAmount
     * @return array
     */
    public static function canEmployeeGetAdvance($employeeId, $requestedAmount = 0)
    {
        $balance = self::getEmployeeAdvanceBalance($employeeId);
        $pendingAdvances = self::where('employee_id', $employeeId)
            ->where('status', self::STATUS_PENDING)
            ->count();

        $canGet = true;
        $reasons = [];

        // قواعد يمكن تخصيصها حسب احتياجات الشركة

        // 1. التحقق من وجود سلف معلقة
        if ($pendingAdvances > 0) {
            $canGet = false;
            $reasons[] = __('hr::lang.employee_has_pending_advances', ['count' => $pendingAdvances]);
        }

        // 2. التحقق من الرصيد المتبقي (مثلاً: لا يمكن طلب سلفة جديدة إذا كان الرصيد أكبر من حد معين)
        // يمكن تعديل هذا الحد حسب سياسة الشركة
        $maxAllowedBalance = 10000; // مثال
        if ($balance > $maxAllowedBalance) {
            $canGet = false;
            $reasons[] = __('hr::lang.employee_balance_exceeds_limit', [
                'balance' => number_format($balance, 2),
                'limit' => number_format($maxAllowedBalance, 2)
            ]);
        }

        return [
            'can_get' => $canGet,
            'current_balance' => $balance,
            'pending_advances' => $pendingAdvances,
            'reasons' => $reasons,
        ];
    }

    /**
     * الحصول على الأقساط القادمة للموظف
     *
     * @param int $employeeId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUpcomingInstallments($employeeId, $limit = 5)
    {
        return HrMonthlyPayment::whereHas('advance', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId)
                  ->where('status', self::STATUS_APPROVED);
        })
        ->where('status', HrMonthlyPayment::STATUS_PENDING ?? 1)
        ->where('due_at', '>=', now())
        ->orderBy('due_at', 'asc')
        ->limit($limit)
        ->with('advance')
        ->get();
    }

    /**
     * الحصول على الأقساط المتأخرة للموظف
     *
     * @param int $employeeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOverdueInstallments($employeeId)
    {
        return HrMonthlyPayment::whereHas('advance', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId)
                  ->where('status', self::STATUS_APPROVED);
        })
        ->where('status', HrMonthlyPayment::STATUS_PENDING ?? 1)
        ->where('due_at', '<', now())
        ->orderBy('due_at', 'asc')
        ->with('advance')
        ->get();
    }

    /**
     * حساب إجمالي الرصيد لجميع الموظفين
     *
     * @return array
     */
    public static function getAllEmployeesBalanceSummary()
    {
        return DB::table('hr_employees as employees')
            ->join('hr_advances as advances', 'employees.id', '=', 'advances.employee_id')
            ->leftJoin('hr_monthly_payments as payments', 'advances.id', '=', 'payments.hr_advance_id')
            ->where('advances.status', self::STATUS_APPROVED)
            ->select(
                'employees.id as employee_id',
                'employees.username as employee_name',
                DB::raw('SUM(DISTINCT advances.amount) as total_approved'),
                DB::raw('SUM(CASE WHEN payments.status = ' . HrMonthlyPayment::STATUS_APPROVED . ' THEN payments.amount ELSE 0 END) as total_paid')
            )
            ->groupBy('employees.id', 'employees.username')
            ->havingRaw('total_approved > total_paid')
            ->orderBy('total_approved', 'desc')
            ->get()
            ->map(function ($item) {
                $item->balance = $item->total_approved - $item->total_paid;
                $item->payment_progress = $item->total_approved > 0 ? ($item->total_paid / $item->total_approved) * 100 : 0;
                return $item;
            });
    }
}
