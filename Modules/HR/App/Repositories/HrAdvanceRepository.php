<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Helpers\TrackerTrait;
use Illuminate\Support\Facades\DB; // Added DB facade
use Modules\HR\App\Repositories\HrMonthlyPaymentRepository; // Added HrMonthlyPaymentRepository
use App\Services\Firebase\FirebaseNotificationService;
use App\Services\pushNotificationService;
class HrAdvanceRepository extends BaseRepository
{
    use TrackerTrait;

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
        'reason'
    ];

    private $HrMonthlyPaymentRepository; // Declare the property
    public function __construct(HrMonthlyPaymentRepository $HrMonthlyPaymentRepository)
    {
        // Inject HrMonthlyPaymentRepository
        parent::__construct(); // Call the parent constructor
        $this->HrMonthlyPaymentRepository = $HrMonthlyPaymentRepository;
    }

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrAdvance::class;
    }

    // Status
    public function statuses()
    {
        return $this->model()::statuses();
    }

    // employees
    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    //Approve
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $hrAdvance = $this->find($id);
            $hrAdvance->status = HrAdvance::STATUS_APPROVED;
            
            // Check if user has an employee relation, otherwise just use auth()->id() or null if it's not strictly required
            $hrAdvance->approver_id = auth()->user()->employee->id ?? auth()->id();
            // Assuming there's a typo in the original code, `approved_id` might actually be `approver_id` based on fillable
            
            $hrAdvance->save();
            
            // Integration with Accounting
            $journalEntry = \Modules\HR\App\Services\HrJournalEntryService::createEntry(
                (float) $hrAdvance->amount,
                'hr_advance_receivable_account',
                'hr_default_cash_bank_account',
                'صرف سلفة للموظف: ' . ($hrAdvance->employee->username ?? 'Unknown'),
                get_class($hrAdvance),
                $hrAdvance->id,
                $hrAdvance->employee_id
            );

            if ($journalEntry) {
                $hrAdvance->journal_entry_id = $journalEntry->id;
                $hrAdvance->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //Reject
    public function reject($id)
    {
        $hrAdvance = $this->find($id);
        $hrAdvance->status = HrAdvance::STATUS_REJECTED;
        $hrAdvance->save();
    }

    public function checkTracking($advance): void
    {

       
        $this->setTracker($advance, $advance->employee_id, HrTracker::TYPE_ADVANCES);
    }

    /**
     * Create a new HrAdvance with monthly payments.
     *
     * @param array $input
     * @param array|null $monthlyPaymentsData
     * @return HrAdvance
     * @throws \Exception
     */
    public function createAdvanceWithMonthlyPayments(array $input, array $monthlyPaymentsData = null): HrAdvance
    {
        DB::beginTransaction();
        try {
            $advance = $this->create($input);

            if ($monthlyPaymentsData && is_array($monthlyPaymentsData)) {
                $totalMonthlyAmount = 0;

                if (count($monthlyPaymentsData) < 1) {
                    throw new \Exception(__('hr::models/hr_advances.minimum_one_installment_required'));
                }

                foreach ($monthlyPaymentsData as $month => $amount) {
                    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                        throw new \Exception(__('hr::models/hr_advances.invalid_month_format', ['month' => $month]));
                    }

                    $amount = (float) $amount;
                    if ($amount <= 0) {
                        throw new \Exception(__('hr::models/hr_advances.invalid_amount_for_month', ['month' => $month]));
                    }

                    $totalMonthlyAmount += $amount;

                    $this->HrMonthlyPaymentRepository->create([
                        'hr_advance_id' => $advance->id,
                        'employee_id' => $advance->employee_id,
                        'due_at' => $month . '-01',
                        'amount' => $amount,
                    ]);
                }

                $advanceAmount = (float) $advance->amount;
                $difference = abs($totalMonthlyAmount - $advanceAmount);

                if ($difference > 0.01) {
                    throw new \Exception(
                        __('hr::lang.monthly_payments_total_mismatch', [
                            'expected' => number_format($advanceAmount, 2),
                            'actual' => number_format($totalMonthlyAmount, 2),
                            'difference' => number_format($difference, 2),
                        ]),
                    );
                }
            } else {
                $this->HrMonthlyPaymentRepository->create([
                    'hr_advance_id' => $advance->id,
                    'employee_id' => $advance->employee_id,
                    'due_at' => $input['from_date'] ?? now()->format('Y-m-d'),
                    'amount' => $advance->amount,
                ]);
            }

            $this->checkTracking($advance);
            DB::commit();
            return $advance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing HrAdvance with monthly payments.
     *
     * @param array $input
     * @param int $id
     * @param array|null $monthlyPaymentsData
     * @return HrAdvance
     * @throws \Exception
     */
    public function updateAdvanceWithMonthlyPayments(array $input, int $id, array $monthlyPaymentsData = null): HrAdvance
    {
        DB::beginTransaction();
        try {
            $advance = $this->find($id);

            if (!$advance) {
                throw new \Exception(__('messages.not_found', replace: ['model' => __('hr:models/hr_advances.singular')]));
            }

            // Handle attachment removal
            if (isset($input['remove_attachment']) && $input['remove_attachment'] == '1') {
                if ($advance->attachment) {
                    $advance->attachment = null;
                    $advance->save();
                }
                unset($input['attachment']);
            }

            // Handle new file upload
            if (isset($input['attachment']) && $input['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $input['attachment'];
                $advance->attachment = $file;
                $advance->save();
                unset($input['attachment']);
            }

            $advance = $this->update($input, $id);

            // --- التحسين: تعديل منطق تحديث الأقساط ---
            // 1. حساب إجمالي المبالغ المدفوعة بالفعل
            $totalPaid = $advance->monthlyPayments()->where('status', '!=', \Modules\HR\App\Models\HrMonthlyPayment::STATUS_PENDING)->sum('amount');

            // 2. التحقق من أن مبلغ السلفة الجديد ليس أقل من المبلغ المدفوع
            if ((float) $advance->amount < $totalPaid) {
                throw new \Exception(
                    __('hr::lang.cannot_update_advance_less_than_paid', [
                        'paid' => number_format($totalPaid, 2),
                        'new_amount' => number_format($advance->amount, 2),
                    ]),
                );
            }

            // 3. حذف الأقساط المعلقة فقط
            $advance->monthlyPayments()->where('status', \Modules\HR\App\Models\HrMonthlyPayment::STATUS_PENDING)->delete();
            // --- نهاية التحسين ---

            if ($monthlyPaymentsData && is_array($monthlyPaymentsData)) {
                $totalMonthlyAmount = 0;

                if (count($monthlyPaymentsData) < 1) {
                    throw new \Exception(__('hr::hr_advances.minimum_one_installment_required'));
                }

                foreach ($monthlyPaymentsData as $month => $amount) {
                    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                        throw new \Exception(__('hr::hr_advances.invalid_month_format', ['month' => $month]));
                    }

                    $amount = (float) $amount;
                    if ($amount <= 0) {
                        throw new \Exception(__('hr::hr_advances.invalid_amount_for_month', ['month' => $month]));
                    }

                    $totalMonthlyAmount += $amount;

                    $this->HrMonthlyPaymentRepository->updateOrCreate(
                        [
                            'hr_advance_id' => $advance->id,
                            'employee_id' => $advance->employee_id,
                            'due_at' => $month . '-01',
                        ],
                        [
                            'amount' => $amount,
                            'status' => \Modules\HR\App\Models\HrMonthlyPayment::STATUS_PENDING,
                        ],
                    );
                }

                $newAdvanceAmount = (float) $advance->amount;
                $expectedInstallmentsTotal = $newAdvanceAmount - $totalPaid;
                $difference = abs($totalMonthlyAmount - $expectedInstallmentsTotal);

                if ($difference > 0.01) {
                    throw new \Exception(
                        __('hr::lang.monthly_payments_total_mismatch', [
                            'expected' => number_format($expectedInstallmentsTotal, 2),
                            'actual' => number_format($totalMonthlyAmount, 2),
                            'difference' => number_format($difference, 2),
                        ]),
                    );
                }
            }

            $this->checkTracking($advance);
            DB::commit();
            return $advance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an HrAdvance and its associated monthly payments.
     *
     * @param int $id
     * @throws \Exception
     */
    public function deleteAdvanceWithMonthlyPayments(int $id): void
    {
        DB::beginTransaction();
        try {
            $advance = $this->find($id);

            if (!$advance) {
                throw new \Exception(__('messages.not_found', ['model' => __('hr:models/hr_advances.singular')]));
            }

            $advance->monthlyPayments()->delete();
            $this->delete($id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get HrAdvance details including employee advance details, approved advances, upcoming and overdue installments.
     *
     * @param int $id
     * @param int $employeeId
     * @return array
     * @throws \Exception
     */
    public function getAdvanceDetails(int $id, int $employeeId): array
    {
        $advance = $this->find($id);

        if (empty($advance)) {
            throw new \Exception(__('hr:models/hr_advances.singular') . ' ' . __('messages.not_found'));
        }

        $balanceDetails = HrAdvance::getEmployeeAdvanceDetails($employeeId);


        return [
            'advance' => $advance,
            'balanceDetails' => $balanceDetails,

        ];
    }
}
