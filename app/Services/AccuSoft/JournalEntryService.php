<?php

namespace App\Services\AccuSoft;

use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\FiscalYear;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Hash;

class JournalEntryService
{
    public function getSetting($key = null, $default = null)
    {
        $settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();

        if (!$settings) {
            $settings = (object) [
                'currency' => 'SAR',
                'decimal_places' => 2,
                'journal_prefix' => 'JE',
                'journal_next_number' => 1,
                'allow_backdated_entries' => false,
                'allow_future_dated_entries' => false,
                'default_depreciation_method' => 1, // Straight Line
                'depreciation_frequency' => 3, // Yearly
                'auto_post_depreciation_entries' => false,
                'vat_enabled' => true,
                'default_vat_rate' => 15.0,
                'custom_settings' => json_encode([]),
            ];
        }

        if (isset($key)) {
            return data_get($settings, $key, $default);
        }

        return $settings;
    }

    /**
     * إنشاء قيد يومي جديد مع التفاصيل
     */
    public function create(array $data): JournalEntry
    {


        return DB::transaction(function () use ($data) {

            $entry = $this->createEntry($data);
            $this->createDetails($entry, $data['details']);
            $this->finalizeEntry($entry);

            return $entry->fresh(['details']);
        });
    }

    /**
     * تعديل قيد موجود
     */
    public function update(JournalEntry $entry, array $data ,string $lock_password = null): JournalEntry
    {

        $this->validateForUpdate($entry, $data ,$lock_password);

        return DB::transaction(function () use ($entry, $data) {


            $this->updateEntry($entry, $data);
            $this->updateDetails($entry, $data['details'] ?? null);
            $this->finalizeEntry($entry);

            return $entry->fresh(['details']);
        });
    }

    /**
     * حذف قيد يومي
     */
    public function delete(JournalEntry $entry, bool $force = false): bool
    {
        $this->validateForDeletion($entry, $force);

        return DB::transaction(function () use ($entry) {
            $entry->details()->delete();
            return $entry->delete();
        });
    }

    /**
     * ترحيل القيد (تغيير الحالة إلى مرحل)
     */
    public function post(JournalEntry $entry): JournalEntry
    {
        $this->validateForPosting($entry);

        return DB::transaction(function () use ($entry) {
            $entry->update([
                'status' => JournalEntry::STATUS_POSTED,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            return $entry->fresh();
        });
    }

    /**
     * عكس القيد (إنشاء قيد معكوس)
     */
    public function reverse(JournalEntry $entry, array $data = []): JournalEntry
    {
        $this->validateForReversal($entry);

        return DB::transaction(function () use ($entry, $data) {
            // إنشاء قيد معكوس
            $reversedEntry = $this->create([
                'entry_date' => $data['entry_date'] ?? now()->format('Y-m-d'),
                'description' => 'عكس قيد رقم: ' . $entry->entry_number . ' - ' . ($data['description'] ?? $entry->description),
                'entry_type' => JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                'status' => JournalEntry::STATUS_POSTED,
                'reference_type' => get_class($entry),
                'reference_id' => $entry->id,
                'details' => $this->getReverseDetails($entry),
            ]);

            // تحديث القيد الأصلي
            $entry->update([
                'status' => JournalEntry::STATUS_REVERSED,
            ]);

            return $reversedEntry;
        });
    }

    // ========================================
    // Protected Helper Methods
    // ========================================

    /**
     * إنشاء القيد الأساسي
     */
    protected function createEntry(array $data): JournalEntry
    {


        $fiscalYearId = $data['fiscal_year_id'] ?? $this->getFiscalYearId($data['entry_date']);

        $entryData = [
            'entry_number' => $data['entry_number'] ?? JournalEntry::generateEntryNumber(),
            'entry_date' => $data['entry_date'],
            'description' => $data['description'] ?? null,
            'branch_id' => $data['branch_id'] ?? (auth()->user()->branch_id ?? null),
            'fiscal_year_id' => $fiscalYearId,
            'entry_type' => $data['entry_type'] ?? JournalEntry::ENTRY_TYPE_MANUAL,
            'source' => $data['source'] ?? JournalEntry::determineSource($data['reference_type'] ?? null, $data['description'] ?? null),
            'status' => $data['status'] ?? JournalEntry::STATUS_DRAFT,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'created_by' => auth()->id(),
        ];

        if (isset($data['attachment'])) {
            $entryData['attachment'] = $data['attachment'];
        }

        return JournalEntry::create($entryData);
    }

    /**
     * إنشاء تفاصيل القيد
     */
    protected function createDetails(JournalEntry $entry, array $details): void
    {
        foreach ($details as $detail) {
            $entry->details()->create([
                'tree_account_id' => $detail['tree_account_id'],
                'debit' => $detail['debit'] ?? 0,
                'credit' => $detail['credit'] ?? 0,
                'description' => $detail['description'] ?? null,
                'cost_center_id' => $detail['cost_center_id'] ?? null,
            ]);
        }
    }

    /**
     * تحديث القيد الأساسي
     */
    protected function updateEntry(JournalEntry $entry, array $data): void
    {
        $updateData = [
            'description' => $data['description'] ?? $entry->description,
            'status' => $data['status'] ?? $entry->status,
            'source' => $data['source'] ?? ($entry->source ?? JournalEntry::determineSource($data['reference_type'] ?? $entry->reference_type, $data['description'] ?? $entry->description)),
            'reference_type' => $data['reference_type'] ?? $entry->reference_type,
            'reference_id' => $data['reference_id'] ?? $entry->reference_id,
            'branch_id' => $data['branch_id']  ?? $entry->branch_id,
        ];



        if (isset($data['attachment'])) {
            $updateData['attachment'] = $data['attachment'] ?? $entry->attachment ;
        }

        // تحديث التاريخ والسنة المالية إذا تغير التاريخ
        // if (isset($data['entry_date']) && $data['entry_date'] != $entry->entry_date) {
        //     $updateData['entry_date'] = $data['entry_date'];

        //     // $updateData['fiscal_year_id'] = $this->getFiscalYearId($data['entry_date']);
        // }


        $entry->update($updateData);
    }

    /**
     * تحديث تفاصيل القيد
     */
    protected function updateDetails(JournalEntry $entry, ?array $details): void
    {
        if (is_null($details)) {
            return;
        }

        // حذف التفاصيل القديمة
        $entry->details()->delete();

        // إضافة التفاصيل الجديدة
        $this->createDetails($entry, $details);
    }

    /**
     * إنهاء إعداد القيد (حساب المجاميع والتحقق من التوازن)
     */
    protected function finalizeEntry(JournalEntry $entry): void
    {
        $entry->calculateTotals();

        if ($entry->status == JournalEntry::STATUS_POSTED) {
            if (!$entry->isBalanced()) {
                throw new Exception('القيد غير متوازن (المدين لا يساوي الدائن)');
            }

            if (!$entry->posted_at) {
                $entry->update([
                    'posted_at' => now(),
                    'posted_by' => auth()->id(),
                ]);
            }
        }

        $entry->save();
    }

    /**
     * الحصول على تفاصيل معكوسة للقيد
     */
    protected function getReverseDetails(JournalEntry $entry): array
    {
        return $entry->details
            ->map(function ($detail) {
                return [
                    'tree_account_id' => $detail->tree_account_id,
                    'debit' => $detail->credit, // عكس المدين والدائن
                    'credit' => $detail->debit,
                    'description' => $detail->description,
                    'cost_center_id' => $detail->cost_center_id,
                ];
            })
            ->toArray();
    }

    /**
     * الحصول على معرف السنة المالية من التاريخ
     */
    protected function getFiscalYearId(string $date): int
    {

        $fiscalYear = FiscalYear::checkDate($date);
        return $fiscalYear->id;
    }

    /**
     * توليد رقم القيد التلقائي
     */
    protected function generateEntryNumber(): string
    {
        $setting = $this->getSetting();
        $prefix = $setting->journal_prefix;

        // Use the journal_next_number from settings as the starting point.
        $nextId = $setting->journal_next_number;

        // Find the last entry number by parsing the string.
        $lastEntry = JournalEntry::latest('id')->first();
        if ($lastEntry && str_starts_with($lastEntry->entry_number, $prefix . '-')) {
            $lastNumber = (int) substr($lastEntry->entry_number, strlen($prefix . '-'));
            // The next number is the maximum of the configured next number and the last actual number + 1.
            $nextId = max($nextId, $lastNumber + 1);
        }

        return $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    // ========================================
    // Validation Methods
    // ========================================

    /**
     * التحقق من إمكانية التعديل
     */
    protected function validateForUpdate(JournalEntry $entry, array $data = [] , string $lock_password = null): void
    {
        $settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();
        $isOpenYear = FiscalYear::isDateInOpenFiscalYear($entry->entry_date);
        $isLocked = $entry->is_locked || !$isOpenYear;




        if ($isLocked) {
            if ($settings && $settings->lock_period_pwd_enabled) {
                $providedPassword = Hash::check($lock_password, $settings->lock_period_pwd) ;

                if (!$providedPassword ) {

                    throw new Exception(__('lang.wrongOldPassword'));
                }


            } else {
                throw new Exception('لا يمكن تعديل قيد مقفل');
            }
        }


        if ($entry->isReversed()) {

            throw new Exception('لا يمكن تعديل قيد معكوس');
        }
    }

    /**
     * التحقق من إمكانية الحذف
     */
    protected function validateForDeletion(JournalEntry $entry, bool $force = false): void
    {
        if ($entry->is_locked) {
            throw new Exception('لا يمكن حذف قيد مقفل');
        }

        // يُسمح بحذف القيود المرحلة في التحديثات الآلية وعند تنظيف القيود التلقائية التي تملك مرجعاً (reference_type)
        if ($entry->isPosted() && ! $force && empty($entry->reference_type)) {
            throw new Exception('لا يمكن حذف قيد مرحل. يجب عكس القيد أولاً');
        }
    }

    /**
     * التحقق من إمكانية الترحيل
     */
    protected function validateForPosting(JournalEntry $entry): void
    {
        if ($entry->isPosted()) {
            throw new Exception('القيد مرحل بالفعل');
        }

        if (!$entry->isBalanced()) {
            throw new Exception('لا يمكن ترحيل قيد غير متوازن');
        }

        if ($entry->details()->count() < 2) {
            throw new Exception('يجب أن يحتوي القيد على سطرين على الأقل');
        }
    }

    /**
     * التحقق من إمكانية العكس
     */
    protected function validateForReversal(JournalEntry $entry): void
    {
        if (!$entry->isPosted()) {
            throw new Exception('يمكن عكس القيود المرحلة فقط');
        }

        if ($entry->isReversed()) {
            throw new Exception('القيد معكوس بالفعل');
        }

        if ($entry->is_locked) {
            throw new Exception('لا يمكن عكس قيد مقفل');
        }
    }

    /**
     * التحقق من كلمة مرور قفل الفترات
     */
    public function verifyLockPassword(string $password): bool
    {
        $settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();

        if (!$settings || !$settings->lock_period_pwd_enabled) {
            return true; // إذا كان القفل غير مفعل برمجياً
        }

        return $password === $settings->lock_period_pwd;
    }

    /**
     * معالجة القيد القادم من السندات أو مصفوفة بيانات خارجية
     */
    public function createJournalEntryFor(array $data): JournalEntry
    {
        return $this->create($data);
    }

    /**
     * تحديث قيد محاسبي قادم من مصفوفة بيانات
     */
    public function updateJournalEntryFor(int $id, array $data): JournalEntry
    {
        $entry = JournalEntry::findOrFail($id);
        return $this->update($entry, $data);
    }
}
