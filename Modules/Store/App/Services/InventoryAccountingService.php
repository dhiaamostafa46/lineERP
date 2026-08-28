<?php

namespace Modules\Store\App\Services;

use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\StoreApp\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\AccuSoft\App\Models\AccountingSettings;

class InventoryAccountingService
{
    /**
     * حساب المخزون العام (Fallback)
     */
    protected function getDefaultInventoryAccount()
    {
        $mapping = AccountMapping::where('mapping_key', 'inventory')->first();
        return $mapping ? $mapping->account_id : null;
    }

    /**
     * إرجاع حساب المستودع أو الحساب الافتراضي
     */
    public function getStoreAccount($storeId)
    {
        if (!$storeId) return $this->getDefaultInventoryAccount();
        
        $store = Store::find($storeId);
        if ($store && $store->tree_account_id) {
            return $store->tree_account_id;
        }

        return $this->getDefaultInventoryAccount();
    }

    /**
     * إنشاء قيد للرصيد الافتتاحي للمخزون
     */
    public function createOpeningBalanceEntry($openingBalanceModel, $totalCost)
    {
        try {
            $inventoryAccountId = $this->getStoreAccount($openingBalanceModel->store_id);
            $capitalAccountId = $this->getMappedAccount('capital');

            if (!$inventoryAccountId || !$capitalAccountId) {
                Log::warning("InventoryAccountingService: Cannot create Opening Balance Entry. Missing accounts.");
                return false;
            }

            if ($totalCost <= 0) return false;

            return $this->buildJournalEntry(
                $openingBalanceModel,
                $totalCost,
                $inventoryAccountId, // المدين (المخزون زاد)
                $capitalAccountId,   // الدائن (رأس المال أو حساب البدء)
                "رصيد افتتاحي للمخزون - " . $openingBalanceModel->document_number,
                JournalEntry::ENTRY_TYPE_AUTO
            );
        } catch (\Exception $e) {
            Log::error("Failed to create Opening Balance Journal Entry: " . $e->getMessage());
            return false;
        }
    }
    

    /**
     * إرجاع الحساب المرتبط بالمفتاح من Mapping
     */
    public function getMappedAccount($key)
    {
        $mapping = AccountMapping::where('mapping_key', $key)->first();
        return $mapping ? $mapping->account_id : null;
    }

    /**
     * إنشاء قيد محاسبي للتالف المحذوف من المخزون
     */
    public function createDamagedEntry($damagedModel, $totalCost)
    {
        try {
            $inventoryAccountId = $this->getStoreAccount($damagedModel->store_id);
            $damageAccountId = $this->getMappedAccount('inventory_damage');

            if (!$inventoryAccountId || !$damageAccountId) {
                Log::warning("InventoryAccountingService: Cannot create Damaged Entry. Missing accounts.");
                return false;
            }

            if ($totalCost <= 0) return false;

            return $this->buildJournalEntry(
                $damagedModel,
                $totalCost,
                $damageAccountId, // المدين (مصروف التالف)
                $inventoryAccountId, // الدائن (المخزون نقص)
                "إثبات تلف لمخزون المستودع رقم " . $damagedModel->store_id,
                JournalEntry::ENTRY_TYPE_AUTO
            );
        } catch (\Exception $e) {
            Log::error("Failed to create Damaged Journal Entry: " . $e->getMessage());
            return false;
        }
    }

    /**
     * إنشاء قيد محاسبي للاستلام المخزني (إثبات دخول بضاعة من مورد)
     */
    public function createReceivingEntry($receivingModel, $totalCost)
    {
        try {
            $inventoryAccountId = $this->getStoreAccount($receivingModel->store_id ?? $receivingModel->to_store_id);
            // Prioritize the account selected in the document, fallback to mapped supplier account
            $creditAccountId = $receivingModel->tree_account_id ?: $this->getMappedAccount('supplier');

            if (!$inventoryAccountId || !$creditAccountId) {
                Log::warning("InventoryAccountingService: Cannot create Receiving Entry. Missing accounts.");
                return false;
            }

            if ($totalCost <= 0) return false;

            return $this->buildJournalEntry(
                $receivingModel,
                $totalCost,
                $inventoryAccountId, // المدين (المخزون زاد)
                $creditAccountId,    // الدائن (الحساب المقابل)
                "إثبات استلام مخزني - " . ($receivingModel->document_number ?? $receivingModel->id),
                JournalEntry::ENTRY_TYPE_AUTO
            );
        } catch (\Exception $e) {
            Log::error("Failed to create Receiving Journal Entry: " . $e->getMessage());
            return false;
        }
    }

    /**
     * إنشاء قيد محاسبي للصرف المخزني (إثبات خروج بضاعة لبيع أو استخدام)
     */
    public function createIssuingEntry($issuingModel, $totalCost)
    {
        try {
            $inventoryAccountId = $this->getStoreAccount($issuingModel->store_id ?? $issuingModel->from_store_id);
            // Prioritize the account selected in the document, fallback to mapped COGS account
            $debitAccountId = $issuingModel->tree_account_id ?: $this->getMappedAccount('cogs');

            if (!$inventoryAccountId || !$debitAccountId) {
                Log::warning("InventoryAccountingService: Cannot create Issuing Entry. Missing accounts.");
                return false;
            }

            if ($totalCost <= 0) return false;

            return $this->buildJournalEntry(
                $issuingModel,
                $totalCost,
                $debitAccountId,     // المدين (الحساب المقابل)
                $inventoryAccountId, // الدائن (المخزون نقص)
                "إثبات صرف مخزني - " . ($issuingModel->document_number ?? $issuingModel->id),
                JournalEntry::ENTRY_TYPE_AUTO
            );
        } catch (\Exception $e) {
            Log::error("Failed to create Issuing Journal Entry: " . $e->getMessage());
            return false;
        }
    }


    /**
     * إنشاء قيد محاسبي للتحويل الصادر (المرحلة الأولى - بضاعة بالطريق)
     */
    public function createTransferOutEntry($transferOutModel, $totalCost)
    {
        $sourceAccountId = $this->getStoreAccount($transferOutModel->from_store_id);
        $inTransitAccountId = $this->getMappedAccount('inventory_in_transit');

        if (!$sourceAccountId || !$inTransitAccountId) {
            Log::warning("createTransferOutEntry: Missing accounts. source={$sourceAccountId}, transit={$inTransitAccountId}");
            return false;
        }

        if ($totalCost <= 0) return false;

        return $this->buildJournalEntry(
            $transferOutModel,
            $totalCost,
            $inTransitAccountId, // المدين (المخزون بالطريق زاد)
            $sourceAccountId, // الدائن (المستودع المصدر نقص)
            "إثبات تحويل مخزون صادر (بضاعة بالطريق) للمستودع المستهدف رقم " . $transferOutModel->to_store_id,
            JournalEntry::ENTRY_TYPE_AUTO
        );
    }

    /**
     * إنشاء قيد محاسبي للتحويل المستلم (المرحلة الثانية - إقفال بضاعة بالطريق)
     */
    public function createTransferInEntry($transferInModel, $totalCost)
    {
        $targetAccountId = $this->getStoreAccount($transferInModel->to_store_id);
        $inTransitAccountId = $this->getMappedAccount('inventory_in_transit');

        if (!$targetAccountId || !$inTransitAccountId) {
            Log::warning("createTransferInEntry: Missing accounts. target={$targetAccountId}, transit={$inTransitAccountId}");
            return false;
        }

        if ($totalCost <= 0) return false;

        return $this->buildJournalEntry(
            $transferInModel,
            $totalCost,
            $targetAccountId, // المدين (المستودع المستلم زاد)
            $inTransitAccountId, // الدائن (المخزون بالطريق نقص)
            "إثبات استلام تحويل مخزون وإقفال بضاعة بالطريق من المستودع المصدر رقم " . $transferInModel->from_store_id,
            JournalEntry::ENTRY_TYPE_AUTO
        );
    }

    /**
     * إنشاء قيد محاسبي لإرجاع التحويل (من بضاعة بالطريق إلى المستودع المصدر)
     */
    public function createTransferReturnEntry($transferModel, $totalCost)
    {
        $sourceAccountId = $this->getStoreAccount($transferModel->from_store_id);
        $inTransitAccountId = $this->getMappedAccount('inventory_in_transit');

        if (!$sourceAccountId || !$inTransitAccountId) {
            Log::warning("createTransferReturnEntry: Missing accounts. source={$sourceAccountId}, transit={$inTransitAccountId}");
            return false;
        }

        if ($totalCost <= 0) return false;

        return $this->buildJournalEntry(
            $transferModel,
            $totalCost,
            $sourceAccountId,    // المدين (المستودع المصدر استعاد بضاعته)
            $inTransitAccountId, // الدائن (المخزون بالطريق نقص)
            "إرجاع تحويل مخزون وإقفال بضاعة بالطريق للمستودع المصدر - " . $transferModel->document_number,
            JournalEntry::ENTRY_TYPE_AUTO
        );
    }

    /**
     * إنشاء قيد محاسبي للتحويل المباشر بين المستودعات
     */
    public function createDirectTransferEntry($transferModel, $totalCost)
    {
        $sourceAccountId = $this->getStoreAccount($transferModel->from_store_id);
        $targetAccountId = $this->getStoreAccount($transferModel->to_store_id);

        if (!$sourceAccountId || !$targetAccountId) {
            Log::warning("createDirectTransferEntry: Missing accounts. source={$sourceAccountId}, target={$targetAccountId}");
            return false;
        }

        if ($totalCost <= 0) return false;

        return $this->buildJournalEntry(
            $transferModel,
            $totalCost,
            $targetAccountId, // المدين (المستودع المستلم زاد)
            $sourceAccountId, // الدائن (المستودع المصدر نقص)
            "تحويل مخزني مباشر من المستودع " . $transferModel->from_store_id . " إلى " . $transferModel->to_store_id,
            JournalEntry::ENTRY_TYPE_AUTO
        );
    }


    /**
     * إنشاء قيد محاسبي لتسويات الجرد
     */
    public function createSettlementEntry($settlementModel, $totalSurplusValue, $totalShortageValue)
    {
        try {
            $inventoryAccountId = $this->getStoreAccount($settlementModel->store_id);
            $lossAccountId = $this->getMappedAccount('inventory_adjustment_loss');
            $profitAccountId = $this->getMappedAccount('inventory_adjustment_profit');

            if (!$inventoryAccountId || (!$lossAccountId && !$profitAccountId)) {
                Log::warning("InventoryAccountingService: Cannot create Settlement Entry. Missing accounts.");
                return false;
            }

            if ($totalSurplusValue <= 0 && $totalShortageValue <= 0) return false;

            DB::beginTransaction();
            $fiscalYear = FiscalYear::where('is_current', true)->first();
            if (!$fiscalYear) {
                throw new \Exception("لا توجد سنة مالية مفعلة.");
            }

            $settings = AccountingSettings::getInstance();
            $isAutoPost = $settings->store_auto_post_journal_entries ?? false;

            $entry = new JournalEntry();
            $entry->entry_number = JournalEntry::generateEntryNumber();
            $entry->entry_date = Carbon::now();
            $entry->description = "إثبات تسوية جردية للمستودع رقم " . $settlementModel->store_id;
            $entry->fiscal_year_id = $fiscalYear->id;
            $entry->entry_type = JournalEntry::ENTRY_TYPE_ADJUSTMENT;
            $entry->source = JournalEntry::SOURCE_STORE;
            $entry->status = $isAutoPost ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_PENDING;
            $entry->total_debit = $totalSurplusValue + $totalShortageValue;
            $entry->total_credit = $totalSurplusValue + $totalShortageValue;
            $entry->created_by = auth()->id() ?? 1;

            if ($isAutoPost) {
                $entry->posted_by = auth()->id() ?? 1;
                $entry->posted_at = Carbon::now();
            } else {
                $entry->posted_by = null;
                $entry->posted_at = null;
            }
            
            if (isset($settlementModel->branch_id)) {
                $entry->branch_id = $settlementModel->branch_id;
            }

            $settlementModel->morphMany(JournalEntry::class, 'reference')->save($entry);

            // Surplus (In) -> Dr Inventory, Cr Profit
            if ($totalSurplusValue > 0) {
                $entry->details()->create([
                    'tree_account_id' => $inventoryAccountId,
                    'debit' => $totalSurplusValue,
                    'credit' => 0,
                    'description' => "زيادة جردية لـ " . $settlementModel->document_number,
                ]);
                $entry->details()->create([
                    'tree_account_id' => $profitAccountId ?: $inventoryAccountId,
                    'debit' => 0,
                    'credit' => $totalSurplusValue,
                    'description' => "إثبات أرباح وعوائد جردية",
                ]);
            }

            // Shortage (Out) -> Dr Loss, Cr Inventory
            if ($totalShortageValue > 0) {
                $entry->details()->create([
                    'tree_account_id' => $lossAccountId ?: $inventoryAccountId,
                    'debit' => $totalShortageValue,
                    'credit' => 0,
                    'description' => "عجز وخسائر جردية",
                ]);
                $entry->details()->create([
                    'tree_account_id' => $inventoryAccountId,
                    'debit' => 0,
                    'credit' => $totalShortageValue,
                    'description' => "نقص في المخزون لـ " . $settlementModel->document_number,
                ]);
            }

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create Settlement Journal Entry: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديث القيد المحاسبي لتسويات الجرد
     */
    public function updateSettlementEntry($settlementModel, $totalSurplusValue, $totalShortageValue)
    {
        try {
            $inventoryAccountId = $this->getStoreAccount($settlementModel->store_id);
            $lossAccountId = $this->getMappedAccount('inventory_adjustment_loss');
            $profitAccountId = $this->getMappedAccount('inventory_adjustment_profit');

            if (!$inventoryAccountId || (!$lossAccountId && !$profitAccountId)) {
                Log::warning("InventoryAccountingService: Cannot update Settlement Entry. Missing accounts.");
                return false;
            }

            $entry = null;
            if ($settlementModel->journal_entry_id) {
                $entry = JournalEntry::find($settlementModel->journal_entry_id);
            }

            if (!$entry) {
                return $this->createSettlementEntry($settlementModel, $totalSurplusValue, $totalShortageValue);
            }

            if ($totalSurplusValue <= 0 && $totalShortageValue <= 0) {
                $entry->details()->delete();
                $entry->update([
                    'total_debit' => 0,
                    'total_credit' => 0,
                ]);
                return $entry;
            }

            DB::beginTransaction();

            $totalAmount = $totalSurplusValue + $totalShortageValue;
            $entry->update([
                'description' => "إثبات تسوية جردية للمستودع رقم " . $settlementModel->store_id . " - " . $settlementModel->document_number,
                'total_debit' => $totalAmount,
                'total_credit' => $totalAmount,
            ]);

            // حذف التفاصيل القديمة وإعادة إنشائها
            $entry->details()->delete();

            // Surplus (In) -> Dr Inventory, Cr Profit
            if ($totalSurplusValue > 0) {
                $entry->details()->create([
                    'tree_account_id' => $inventoryAccountId,
                    'debit' => $totalSurplusValue,
                    'credit' => 0,
                    'description' => "زيادة جردية لـ " . $settlementModel->document_number,
                ]);
                $entry->details()->create([
                    'tree_account_id' => $profitAccountId ?: $inventoryAccountId,
                    'debit' => 0,
                    'credit' => $totalSurplusValue,
                    'description' => "إثبات أرباح وعوائد جردية",
                ]);
            }

            // Shortage (Out) -> Dr Loss, Cr Inventory
            if ($totalShortageValue > 0) {
                $entry->details()->create([
                    'tree_account_id' => $lossAccountId ?: $inventoryAccountId,
                    'debit' => $totalShortageValue,
                    'credit' => 0,
                    'description' => "عجز وخسائر جردية",
                ]);
                $entry->details()->create([
                    'tree_account_id' => $inventoryAccountId,
                    'debit' => 0,
                    'credit' => $totalShortageValue,
                    'description' => "نقص في المخزون لـ " . $settlementModel->document_number,
                ]);
            }

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update Settlement Journal Entry: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Core Function لبناء هيكل القيد
     */
    public function buildJournalEntry($model, $amount, $debitAccountId, $creditAccountId, $description, $type)
    {
        DB::beginTransaction();
        try {
            $fiscalYear = FiscalYear::where('is_current', true)->first();
            if (!$fiscalYear) {
                throw new \Exception("لا توجد سنة مالية مفعلة.");
            }

            $settings = AccountingSettings::getInstance();
            $isAutoPost = $settings->store_auto_post_journal_entries ?? false;

            $entry = new JournalEntry();
            $entry->entry_number = JournalEntry::generateEntryNumber();
            $entry->entry_date = Carbon::now();
            $entry->description = $description;
            $entry->fiscal_year_id = $fiscalYear->id;
            $entry->entry_type = $type;
            $entry->source = JournalEntry::SOURCE_STORE;
            $entry->status = $isAutoPost ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_PENDING;
            $entry->total_debit = $amount;
            $entry->total_credit = $amount;
            $entry->created_by = auth()->id() ?? 1; // Fallback for tests
            
            if ($isAutoPost) {
                $entry->posted_by = auth()->id() ?? 1;
                $entry->posted_at = Carbon::now();
            }
            
            // Branch from model if available
            if (isset($model->branch_id)) {
                $entry->branch_id = $model->branch_id;
            }

            // Polymorphic relation
            $model->morphMany(JournalEntry::class, 'reference')->save($entry);

            // Details - المدين
            $entry->details()->create([
                'tree_account_id' => $debitAccountId,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
            ]);

            // Details - الدائن
            $entry->details()->create([
                'tree_account_id' => $creditAccountId,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
            ]);

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حذف كافة القيود المحاسبية المرتبطة بسند معين
     */
    public function deleteJournalEntries($model)
    {
        try {
            DB::beginTransaction();
            foreach ($model->journalEntries as $entry) {
                // حذف التفاصيل أولاً ثم القيد نهائياً (forceDelete) لتحرير entry_number
                $entry->details()->forceDelete();
                $entry->forceDelete();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete journal entries: " . $e->getMessage());
            return false;
        }
    }
}
