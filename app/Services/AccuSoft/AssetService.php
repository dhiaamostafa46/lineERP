<?php

namespace App\Services\AccuSoft;

use Illuminate\Support\Facades\DB;
use Modules\AccuSoft\App\Models\Asset;
use App\Models\AccuSoft\JournalEntry;
use Carbon\Carbon;
use Exception;

class AssetService
{
    protected JournalEntryService $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    /**
     * Create an asset and generate the purchase Journal Entry
     */
    public function purchaseAsset(array $data, ?int $paymentAccountId = null, string $paymentDescription = ''): Asset
    {
        return DB::transaction(function () use ($data, $paymentAccountId, $paymentDescription) {
            
            $status = $data['depreciation_status'] ?? \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE;
            $method = $data['depreciation_method'] ?? null;
            if (is_string($method) && !is_numeric($method)) {
                if ($method == 'none') {
                    $data['depreciation_method'] = 0;
                } else {
                    $data['depreciation_method'] = $method == 'straight_line' ? 1 : ($method == 'declining_balance' ? 2 : 0);
                }
            }

            // Defaults if "none"
            if ($status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) {
                $data['purchase_date'] = $data['purchase_date'] ?? now();
                $data['purchase_value'] = $data['purchase_value'] ?? 0;
                $data['salvage_value'] = $data['salvage_value'] ?? 0;
                $data['asset_account_id'] = null;
                $data['accumulated_depreciation_account_id'] = null;
                $data['depreciation_expense_account_id'] = null;
                
                $asset = Asset::create($data);
                
                // No journal entry, no transaction record. Just data.
                return $asset;
            }

            if ($status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY) {
                // Takes accounts directly from Category
                $category = \Modules\AccuSoft\App\Models\AssetCategory::find($data['asset_category_id']);
                if (!$category) {
                    throw new \Exception('الفئة المحددة غير موجودة.');
                }
                $data['asset_account_id'] = $category->asset_account_id;
                $data['accumulated_depreciation_account_id'] = $category->accumulated_depreciation_account_id;
                $data['depreciation_expense_account_id'] = $category->depreciation_expense_account_id;
            } else {
                // status == 'custom'
                // 1. Get Parent Accounts from AccountMapping
                $assetParentId = $data['parent_account_id'] ?? null;
                $accumDeprParentId = \App\Models\AccuSoft\AccountMapping::getAccountId('accumulated_depreciation');
                $deprExpenseParentId = \App\Models\AccuSoft\AccountMapping::getAccountId('Expenseasste_depreciation');
                
                if (!$assetParentId || !$accumDeprParentId || !$deprExpenseParentId) {
                    throw new \Exception('لم يتم تحديد الحسابات الرئيسية للأصول في إعدادات الربط المحاسبي (Account Mapping). يرجى تعيين (accumulated_depreciation, Expenseasste_depreciation).');
                }

                // 2. Generate TreeAccounts
                $assetParent = \App\Models\AccuSoft\TreeAccounts::find($assetParentId);
                $assetAccountData = [
                    'parent_id' => $assetParent->id,
                    'code' => \App\Models\AccuSoft\TreeAccounts::generateCode($assetParent->id),
                    'account_type' => $assetParent->account_type,
                    'status' => \App\Models\AccuSoft\TreeAccounts::STATUS_ACTIVE,
                ];
                
                $accumDeprParent = \App\Models\AccuSoft\TreeAccounts::find($accumDeprParentId);
                $accumDeprAccountData = [
                    'parent_id' => $accumDeprParent->id,
                    'code' => \App\Models\AccuSoft\TreeAccounts::generateCode($accumDeprParent->id),
                    'account_type' => $accumDeprParent->account_type,
                    'status' => \App\Models\AccuSoft\TreeAccounts::STATUS_ACTIVE,
                ];

                $deprExpenseParent = \App\Models\AccuSoft\TreeAccounts::find($deprExpenseParentId);
                $deprExpenseAccountData = [
                    'parent_id' => $deprExpenseParent->id,
                    'code' => \App\Models\AccuSoft\TreeAccounts::generateCode($deprExpenseParent->id),
                    'account_type' => $deprExpenseParent->account_type,
                    'status' => \App\Models\AccuSoft\TreeAccounts::STATUS_ACTIVE,
                ];
                
                $locales = config('langs', ['en' => 'English', 'ar' => 'Arabic']);
                foreach ($locales as $locale => $language) {
                    $name = $data[$locale]['name'] ?? ($data['ar']['name'] ?? 'Asset');
                    $assetAccountData[$locale] = ['name' => $name];
                    $accumDeprAccountData[$locale] = ['name' => 'مجمع إهلاك - ' . $name];
                    $deprExpenseAccountData[$locale] = ['name' => 'مصروف إهلاك - ' . $name];
                }

                $assetAccount = \App\Models\AccuSoft\TreeAccounts::create($assetAccountData);
                $accumDeprAccount = \App\Models\AccuSoft\TreeAccounts::create($accumDeprAccountData);
                $deprExpenseAccount = \App\Models\AccuSoft\TreeAccounts::create($deprExpenseAccountData);

                $data['asset_account_id'] = $assetAccount->id;
                $data['accumulated_depreciation_account_id'] = $accumDeprAccount->id;
                $data['depreciation_expense_account_id'] = $deprExpenseAccount->id;
            }

            $taxAmount = 0;
            $purchaseValue = (float) $data['purchase_value'];
            $assetCost = $purchaseValue;
            
            if (!empty($data['tax_amount'])) {
                $taxAccount = \App\Models\AccuSoft\TaxAccount::find($data['tax_amount']);
                if ($taxAccount) {
                    $rate = (float) $taxAccount->rate;
                    $taxType = $data['tax_type'] ?? 'exclusive';
                    
                    if ($taxType == 'inclusive') {
                        $assetCost = round($purchaseValue / (1 + ($rate / 100)), 2);
                        $taxAmount = round($purchaseValue - $assetCost, 2);
                    } else {
                        $taxAmount = round($purchaseValue * ($rate / 100), 2);
                        $assetCost = $purchaseValue;
                    }
                }
            }

            // 3. Create the Asset Record
            $assetData = \Illuminate\Support\Arr::except($data, ['parent_account_id']);
            $assetData['purchase_value'] = $assetCost;
            $assetData['status'] = $data['status'] ?? Asset::STATUS_ACTIVE;
            $asset = Asset::create($assetData);

            // 4. Generate Journal Entry for Purchase
            $entryDate = $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : now()->format('Y-m-d');
            if (!\App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($entryDate)) {
                $today = now()->format('Y-m-d');
                if (\App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($today)) {
                    $entryDate = $today;
                } else {
                    $openYear = \App\Models\AccuSoft\FiscalYear::open()->orderBy('start_date', 'asc')->first();
                    if ($openYear) {
                        $entryDate = $openYear->start_date->format('Y-m-d');
                    }
                }
                if (function_exists('flash')) {
                    flash()->warning('تاريخ الشراء يقع في فترة مالية مغلقة. تم إثبات قيد الشراء في أول فترة مالية مفتوحة متاحة.');
                }
            }

            $journalData = [
                'entry_date' => $entryDate,
                'description' => __('lang.asset_purchase_entry', ['code' => $asset->code]) ?? 'شراء أصل ثابت: ' . $asset->code,
                'entry_type' => JournalEntry::ENTRY_TYPE_MANUAL,
                'source' => JournalEntry::SOURCE_ASSETS,
                'status' => JournalEntry::STATUS_POSTED,
                'reference_type' => Asset::class,
                'reference_id' => $asset->id,
                'details' => [
                    [
                        'tree_account_id' => $asset->asset_account_id,
                        'debit' => $assetCost,
                        'credit' => 0,
                        'description' => $paymentDescription ?: 'قيمة الأصل الثابت',
                        'cost_center_id' => $asset->cost_center_id,
                    ]
                ]
            ];

            // If Tax is present, we add it to the debit side
            if ($taxAmount > 0) {
                $taxAccountId = \App\Models\AccuSoft\AccountMapping::getAccountId('purchase_tax') ?? \App\Models\AccuSoft\AccountMapping::getAccountId('tax');
                if (!$taxAccountId) {
                    throw new \Exception('لم يتم العثور على حساب الضريبة (purchase_tax أو tax) في الربط المحاسبي (Account Mapping).');
                }
                
                $journalData['details'][] = [
                    'tree_account_id' => $taxAccountId,
                    'debit' => $taxAmount,
                    'credit' => 0,
                    'description' => 'ضريبة قيمة الأصل الثابت',
                    'cost_center_id' => $asset->cost_center_id,
                ];
            }

            if ($paymentAccountId) {
                $journalData['details'][] = [
                    'tree_account_id' => $paymentAccountId,
                    'debit' => 0,
                    'credit' => $assetCost + $taxAmount, // Total Payment includes tax
                    'description' => $paymentDescription ?: 'دفع قيمة الأصل',
                    'cost_center_id' => $asset->cost_center_id,
                ];
            }

            $journalEntry = $this->journalEntryService->create($journalData);

            if ($asset->calculation_type === 'automatic' && $asset->depreciation_status !== Asset::DEPRECIATION_STATUS_NONE) {
                $this->generateDepreciationSchedule($asset);
                $this->catchUpDepreciation($asset);
            }

            // 5. Log Asset Transaction
            \Modules\AccuSoft\App\Models\AssetTransaction::create([
                'asset_id' => $asset->id,
                'transaction_type' => 'purchase',
                'transaction_date' => $asset->purchase_date,
                'amount' => $asset->purchase_value,
                'notes' => 'شراء وتفعيل الأصل المالي',
                'journal_entry_id' => $journalEntry->id ?? null,
            ]);

            return $asset;
        });
    }

    /**
     * Updates the original purchase transaction and its journal entry
     */
    public function updatePurchaseEntry(Asset $asset, array $data): void
    {
        // 1. Recalculate Tax and Asset Cost
        $taxAmount = 0;
        $purchaseValue = (float) $data['purchase_value'];
        $assetCost = $purchaseValue;
        $paymentAccountId = $data['payment_account_id'] ?? null;
        
        if (!empty($data['tax_amount'])) {
            $taxAccount = \App\Models\AccuSoft\TaxAccount::find($data['tax_amount']);
            if ($taxAccount) {
                $rate = (float) $taxAccount->rate;
                $taxType = $data['tax_type'] ?? 'exclusive';
                
                if ($taxType == 'inclusive') {
                    $assetCost = round($purchaseValue / (1 + ($rate / 100)), 2);
                    $taxAmount = round($purchaseValue - $assetCost, 2);
                } else {
                    $taxAmount = round($purchaseValue * ($rate / 100), 2);
                    $assetCost = $purchaseValue;
                }
            }
        }

        // 2. Find original purchase transaction
        $transaction = $asset->transactions()->where('transaction_type', 'purchase')->first();
        if (!$transaction) return;

        // 3. Update the transaction record
        $transaction->update([
            'amount' => $assetCost,
            'transaction_date' => $data['purchase_date'] ?? $asset->purchase_date,
        ]);

        // 4. Update the corresponding Journal Entry
        $journalEntryId = $transaction->journal_entry_id;
        if (!$journalEntryId) {
            $je = \App\Models\AccuSoft\JournalEntry::where('reference_type', Asset::class)
                ->where('reference_id', $asset->id)
                ->first();
            if ($je) {
                $journalEntryId = $je->id;
                $transaction->update(['journal_entry_id' => $journalEntryId]);
            }
        }

        if ($journalEntryId) {
            $journalEntry = \App\Models\AccuSoft\JournalEntry::find($journalEntryId);
            if ($journalEntry) {
                $entryDate = $data['purchase_date'] ?? $asset->purchase_date;
                $entryDate = is_string($entryDate) ? $entryDate : $entryDate->format('Y-m-d');
                
                if (!\App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($entryDate)) {
                    $today = now()->format('Y-m-d');
                    if (\App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($today)) {
                        $entryDate = $today;
                    } else {
                        $openYear = \App\Models\AccuSoft\FiscalYear::open()->orderBy('start_date', 'asc')->first();
                        if ($openYear) {
                            $entryDate = $openYear->start_date->format('Y-m-d');
                        }
                    }
                    if (function_exists('flash')) {
                        flash()->warning('تاريخ الشراء يقع في فترة مالية مغلقة. تم إثبات قيد الشراء المُعدّل في أول فترة مالية مفتوحة متاحة.');
                    }
                }

                $journalData = [
                    'entry_date' => $entryDate,
                    'details' => [
                        [
                            'tree_account_id' => $asset->asset_account_id,
                            'debit' => $assetCost,
                            'credit' => 0,
                            'description' => 'قيمة الأصل الثابت (معدل)',
                            'cost_center_id' => $asset->cost_center_id,
                        ]
                    ]
                ];

                if ($taxAmount > 0) {
                    $taxAccountId = \App\Models\AccuSoft\AccountMapping::getAccountId('purchase_tax') ?? \App\Models\AccuSoft\AccountMapping::getAccountId('tax');
                    if ($taxAccountId) {
                        $journalData['details'][] = [
                            'tree_account_id' => $taxAccountId,
                            'debit' => $taxAmount,
                            'credit' => 0,
                            'description' => 'ضريبة قيمة الأصل الثابت (معدل)',
                            'cost_center_id' => $asset->cost_center_id,
                        ];
                    }
                }

                if ($paymentAccountId) {
                    $journalData['details'][] = [
                        'tree_account_id' => $paymentAccountId,
                        'debit' => 0,
                        'credit' => $assetCost + $taxAmount,
                        'description' => 'دفع قيمة الأصل (معدل)',
                        'cost_center_id' => $asset->cost_center_id,
                    ];
                }

                // Unpost if needed, or simply update. The JournalEntryService update method validates and updates details.
                try {
                    $this->journalEntryService->update($journalEntry, $journalData);
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Run depreciation for a specific asset
     */
    public function depreciateAsset(Asset $asset, Carbon $date, ?float $manualAmount = null, string $notes = ''): bool
    {
        if ($asset->status != Asset::STATUS_ACTIVE || ($asset->current_book_value !== null && $asset->current_book_value <= $asset->salvage_value)) {
            return false;
        }

        return DB::transaction(function () use ($asset, $date, $manualAmount, $notes) {
            if ($asset->calculation_type === 'manual' && $manualAmount !== null) {
                $amount = $manualAmount;
            } else {
                $amount = $asset->calculateDepreciationAmount();
            }
            
            if ($amount <= 0) return false;

            $currentBookValue = $asset->current_book_value ?? $asset->purchase_value;
            if (($currentBookValue - $amount) < $asset->salvage_value) {
                $amount = $currentBookValue - $asset->salvage_value;
            }

            if ($amount <= 0) {
                if ($asset->status != Asset::STATUS_FULLY_DEPRECIATED) {
                    $asset->update(['status' => Asset::STATUS_FULLY_DEPRECIATED]);
                }
                return false;
            }
            
            // Generate Journal Entry if asset has accounting effect
            $hasAccountingEffect = $asset->assetCategory ? $asset->assetCategory->has_accounting_effect : true;
            $journalEntryId = null;

            if ($hasAccountingEffect && $asset->depreciation_expense_account_id && $asset->accumulated_depreciation_account_id) {
                $description = __('lang.asset_depreciation_entry', ['code' => $asset->code, 'month' => $date->month, 'year' => $date->year]) ?? "إهلاك الأصل $asset->code لشهر {$date->month} - {$date->year}";
                if ($notes) {
                    $description .= ' - ملاحظات: ' . $notes;
                }

                $journalEntry = $this->journalEntryService->create([
                'entry_date' => $date->endOfMonth()->format('Y-m-d'),
                'description' => $description,
                'entry_type' => JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                'status' => JournalEntry::STATUS_POSTED,
                'reference_type' => Asset::class,
                'reference_id' => $asset->id,
                'details' => [
                    [
                        'tree_account_id' => $asset->depreciation_expense_account_id,
                        'debit' => $amount,
                        'credit' => 0,
                        'description' => 'مصروف إهلاك',
                        'cost_center_id' => $asset->cost_center_id,
                    ],
                    [
                        'tree_account_id' => $asset->accumulated_depreciation_account_id,
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => 'مجمع الإهلاك',
                        'cost_center_id' => $asset->cost_center_id,
                    ]
                ]
            ]);
                $journalEntryId = $journalEntry->id;
            }

            // Create Depreciation Record
            $asset->depreciations()->create([
                'year' => $date->year,
                'month' => $date->month,
                'period_date' => $date->endOfMonth(),
                'depreciation_amount' => $amount,
                'accumulated_depreciation' => $asset->total_depreciation + $amount,
                'book_value' => $currentBookValue - $amount,
                'journal_entry_id' => $journalEntryId,
                'entry_type' => 1, // monthly
                'is_posted' => $journalEntryId ? true : false,
            ]);

            Asset::where('id', $asset->id)->update([
                'total_depreciation' => $asset->total_depreciation + $amount,
                'last_depreciation_date' => $date,
                'next_depreciation_date' => $date->copy()->addMonth(),
                'status' => ($currentBookValue - $amount) <= $asset->salvage_value ? Asset::STATUS_FULLY_DEPRECIATED : $asset->status,
            ]);

            return true;
        });
    }

    /**
     * Dispose of the asset and generate Journal Entry
     */
    public function disposeAsset(Asset $asset, Carbon $date, float $disposalValue, int $disposalType, int $cashAccountId): bool
    {
        return DB::transaction(function () use ($asset, $date, $disposalValue, $disposalType, $cashAccountId) {
            $bookValue = $asset->current_book_value ?? $asset->purchase_value;
            $gainLoss = $disposalValue - $bookValue;

            $gainLossAccountId = null;
            if ($gainLoss != 0) {
                $gainLossAccountId = $this->getGainLossAccount($gainLoss);
            }

            // Prepare Journal Entry Details
            $details = [];
            
            $hasAccountingEffect = $asset->assetCategory ? $asset->assetCategory->has_accounting_effect : true;

            // 1. Debit Cash/Bank (if sold)
            if ($disposalValue > 0) {
                $details[] = [
                    'tree_account_id' => $cashAccountId,
                    'debit' => $disposalValue,
                    'credit' => 0,
                    'description' => 'قيمة استبعاد الأصل',
                    'cost_center_id' => $asset->cost_center_id,
                ];
            }

            // 2. Debit Accumulated Depreciation (to close it)
            if ($asset->total_depreciation > 0) {
                $details[] = [
                    'tree_account_id' => $asset->accumulated_depreciation_account_id,
                    'debit' => $asset->total_depreciation,
                    'credit' => 0,
                    'description' => 'إقفال مجمع إهلاك الأصل',
                    'cost_center_id' => $asset->cost_center_id,
                ];
            }

            // 3. Credit Asset Account (to close the historical cost)
            $details[] = [
                'tree_account_id' => $asset->asset_account_id,
                'debit' => 0,
                'credit' => $asset->purchase_value,
                'description' => 'إقفال تكلفة الأصل الثابت',
                'cost_center_id' => $asset->cost_center_id,
            ];

            // 4. Gain / Loss Account
            if ($gainLoss > 0) {
                if (!$gainLossAccountId) {
                    throw new \Exception('لم يتم العثور على حساب أرباح بيع الأصول الثابتة ولم يتمكن النظام من إنشائه.');
                }
                // Gain -> Credit
                $details[] = [
                    'tree_account_id' => $gainLossAccountId,
                    'debit' => 0,
                    'credit' => $gainLoss,
                    'description' => 'أرباح استبعاد الأصول',
                    'cost_center_id' => $asset->cost_center_id,
                ];
            } elseif ($gainLoss < 0) {
                if (!$gainLossAccountId) {
                    throw new \Exception('لم يتم العثور على حساب خسائر بيع الأصول الثابتة ولم يتمكن النظام من إنشائه.');
                }
                // Loss -> Debit
                $details[] = [
                    'tree_account_id' => $gainLossAccountId,
                    'debit' => abs($gainLoss),
                    'credit' => 0,
                    'description' => 'خسائر استبعاد الأصول',
                    'cost_center_id' => $asset->cost_center_id,
                ];
            }

            // Generate Journal Entry
            $journalEntry = $this->journalEntryService->create([
                'entry_date' => $date->format('Y-m-d'),
                'description' => __('lang.asset_disposal_entry', ['code' => $asset->code]) ?? "استبعاد الأصل الثابت: $asset->code",
                'entry_type' => JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                'source' => JournalEntry::SOURCE_ASSETS,
                'status' => JournalEntry::STATUS_POSTED,
                'reference_type' => Asset::class,
                'reference_id' => $asset->id,
                'details' => $details
            ]);

            // Update Asset status
            Asset::where('id', $asset->id)->update([
                'status' => Asset::STATUS_DISPOSED,
                'disposal_date' => $date,
                'disposal_value' => $disposalValue,
                'disposal_type' => $disposalType,
                'disposal_journal_entry_id' => $journalEntry->id,
                'disposal_gain_loss' => $gainLoss,
            ]);

            // Log Asset Transaction
            \Modules\AccuSoft\App\Models\AssetTransaction::create([
                'asset_id' => $asset->id,
                'transaction_type' => 'disposal',
                'transaction_date' => $date,
                'amount' => $disposalValue,
                'notes' => 'استبعاد الأصل (' . ($gainLoss > 0 ? 'ربح' : ($gainLoss < 0 ? 'خسارة' : 'بدون ربح أو خسارة')) . ')',
                'journal_entry_id' => $journalEntry->id,
            ]);

            return true;
        });
    }

    /**
     * Run depreciation for all active assets for a specific month and year
     */
    public function batchDepreciationRun(int $month, int $year, int $userId, string $notes = '', bool $usesIndividualEntries = false): \Modules\AccuSoft\App\Models\DepreciationRun
    {
        return DB::transaction(function () use ($month, $year, $userId, $notes, $usesIndividualEntries) {
            $date = Carbon::create($year, $month, 1)->endOfMonth();
            $assets = Asset::where('status', Asset::STATUS_ACTIVE)
                ->where('calculation_type', 'automatic')
                ->where(function($query) use ($date) {
                    $query->whereNull('next_depreciation_date')
                          ->orWhere('next_depreciation_date', '<=', $date);
                })
                ->get();

            $totalDepreciation = 0;
            $details = [];
            $depreciatedAssets = [];

            foreach ($assets as $asset) {
                // Check if already fully depreciated mathematically
                if ($asset->current_book_value !== null && $asset->current_book_value <= $asset->salvage_value) {
                    continue;
                }

                $amount = $asset->calculateDepreciationAmount();
                if ($amount <= 0) continue;

                $currentBookValue = $asset->current_book_value ?? $asset->purchase_value;
                if (($currentBookValue - $amount) < $asset->salvage_value) {
                    $amount = $currentBookValue - $asset->salvage_value;
                }

                if ($amount <= 0) continue;

                $totalDepreciation += $amount;

                // Prepare Journal Entry Lines (Debit Expense, Credit Accumulated)
                $details[] = [
                    'tree_account_id' => $asset->depreciation_expense_account_id,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "مصروف إهلاك - {$asset->name}",
                    'cost_center_id' => $asset->cost_center_id,
                ];
                $details[] = [
                    'tree_account_id' => $asset->accumulated_depreciation_account_id,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "مجمع إهلاك - {$asset->name}",
                    'cost_center_id' => $asset->cost_center_id,
                ];

                $depreciatedAssets[] = [
                    'asset' => $asset,
                    'amount' => $amount,
                    'currentBookValue' => $currentBookValue,
                ];
            }

            if ($totalDepreciation <= 0) {
                throw new \Exception('لا توجد أصول تتطلب إهلاكاً لهذه الفترة.');
            }

            if (!$usesIndividualEntries) {
                // Create single bulk Journal Entry
                $bulkJournalEntry = $this->journalEntryService->create([
                    'entry_date' => $date->format('Y-m-d'),
                    'description' => "دورة الإهلاك المجمعة لشهر $month - $year",
                    'entry_type' => JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                    'source' => JournalEntry::SOURCE_ASSETS,
                    'status' => JournalEntry::STATUS_POSTED,
                    'details' => $details
                ]);
            }

            // Create Depreciation Run Record
            $run = \Modules\AccuSoft\App\Models\DepreciationRun::create([
                'run_name' => "إهلاك $month/$year",
                'run_date' => now(),
                'run_month' => $month,
                'run_year' => $year,
                'total_depreciation' => $totalDepreciation,
                'journal_entry_id' => $usesIndividualEntries ? null : $bulkJournalEntry->id,
                'status' => 'completed',
                'notes' => $notes,
                'created_by' => $userId,
                'uses_individual_entries' => $usesIndividualEntries,
            ]);

            // Process Each Asset
            foreach ($depreciatedAssets as $item) {
                $asset = $item['asset'];
                $amount = $item['amount'];
                $currentBookValue = $item['currentBookValue'];

                $currentEntryId = $usesIndividualEntries ? null : $bulkJournalEntry->id;

                if ($usesIndividualEntries) {
                    $individualEntry = $this->journalEntryService->create([
                        'entry_date' => $date->format('Y-m-d'),
                        'description' => "إهلاك الأصل {$asset->name} لشهر $month - $year",
                        'entry_type' => JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                        'source' => JournalEntry::SOURCE_ASSETS,
                        'status' => JournalEntry::STATUS_POSTED,
                        'reference_type' => Asset::class,
                        'reference_id' => $asset->id,
                        'details' => [
                            [
                                'tree_account_id' => $asset->depreciation_expense_account_id,
                                'debit' => $amount,
                                'credit' => 0,
                                'description' => "مصروف إهلاك - {$asset->name}",
                                'cost_center_id' => $asset->cost_center_id,
                            ],
                            [
                                'tree_account_id' => $asset->accumulated_depreciation_account_id,
                                'debit' => 0,
                                'credit' => $amount,
                                'description' => "مجمع إهلاك - {$asset->name}",
                                'cost_center_id' => $asset->cost_center_id,
                            ]
                        ]
                    ]);
                    $currentEntryId = $individualEntry->id;
                }

                // Record asset depreciation history
                $asset->depreciations()->create([
                    'year' => $year,
                    'month' => $month,
                    'period_date' => $date,
                    'depreciation_amount' => $amount,
                    'accumulated_depreciation' => $asset->total_depreciation + $amount,
                    'book_value' => $currentBookValue - $amount,
                    'journal_entry_id' => $currentEntryId,
                    'entry_type' => 1,
                    'is_posted' => true,
                    'depreciation_run_id' => $run->id,
                    'cost_center_id' => $asset->cost_center_id,
                ]);

                // Update Asset
                Asset::where('id', $asset->id)->update([
                    'total_depreciation' => $asset->total_depreciation + $amount,
                    'last_depreciation_date' => $date,
                    'next_depreciation_date' => $date->copy()->addMonth(),
                    'status' => ($currentBookValue - $amount) <= $asset->salvage_value ? Asset::STATUS_FULLY_DEPRECIATED : $asset->status,
                ]);
            }

            return $run;
        });
    }

    public function executeScheduledDepreciation(Asset $asset, int $depreciationId, Carbon $executionDate, string $notes = ''): bool
    {
        if ($asset->status != Asset::STATUS_ACTIVE || ($asset->current_book_value !== null && $asset->current_book_value <= $asset->salvage_value)) {
            return false;
        }

        $depreciation = $asset->depreciations()->where('id', $depreciationId)->where('is_posted', false)->first();
        if (!$depreciation) {
            return false;
        }

        return DB::transaction(function () use ($asset, $depreciation, $executionDate, $notes) {
            $amount = $depreciation->depreciation_amount;
            if ($amount <= 0) return false;

            $currentBookValue = $asset->current_book_value ?? $asset->purchase_value;
            if (($currentBookValue - $amount) < $asset->salvage_value) {
                $amount = $currentBookValue - $asset->salvage_value;
            }

            if ($amount <= 0) {
                if ($asset->status != Asset::STATUS_FULLY_DEPRECIATED) {
                    $asset->update(['status' => Asset::STATUS_FULLY_DEPRECIATED]);
                }
                return false;
            }

            // Generate Journal Entry if asset has accounting effect
            $hasAccountingEffect = $asset->assetCategory ? $asset->assetCategory->has_accounting_effect : true;
            $journalEntryId = null;

            if ($hasAccountingEffect && $asset->depreciation_expense_account_id && $asset->accumulated_depreciation_account_id) {
                $description = __('lang.asset_depreciation_entry', ['code' => $asset->code, 'month' => $executionDate->month, 'year' => $executionDate->year]) ?? "إهلاك الأصل $asset->code لشهر {$executionDate->month} - {$executionDate->year}";
                if ($notes) {
                    $description .= ' - ملاحظات: ' . $notes;
                }

                $journalEntry = $this->journalEntryService->create([
                    'entry_date' => $executionDate->format('Y-m-d'),
                    'description' => $description,
                    'entry_type' => JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                    'status' => JournalEntry::STATUS_POSTED,
                    'reference_type' => Asset::class,
                    'reference_id' => $asset->id,
                    'details' => [
                        [
                            'tree_account_id' => $asset->depreciation_expense_account_id,
                            'debit' => $amount,
                            'credit' => 0,
                            'description' => 'مصروف إهلاك',
                            'cost_center_id' => $asset->cost_center_id,
                        ],
                        [
                            'tree_account_id' => $asset->accumulated_depreciation_account_id,
                            'debit' => 0,
                            'credit' => $amount,
                            'description' => 'مجمع الإهلاك',
                            'cost_center_id' => $asset->cost_center_id,
                        ]
                    ]
                ]);
                $journalEntryId = $journalEntry->id;
            }

            $depreciation->update([
                'depreciation_amount' => $amount,
                'accumulated_depreciation' => $asset->total_depreciation + $amount,
                'book_value' => $currentBookValue - $amount,
                'journal_entry_id' => $journalEntryId,
                'is_posted' => true,
            ]);

            Asset::where('id', $asset->id)->update([
                'total_depreciation' => $asset->total_depreciation + $amount,
                'current_book_value' => $currentBookValue - $amount,
                'last_depreciation_date' => $executionDate,
                'next_depreciation_date' => $executionDate->copy()->addMonth(),
                'status' => ($currentBookValue - $amount) <= $asset->salvage_value ? Asset::STATUS_FULLY_DEPRECIATED : $asset->status,
            ]);

            return true;
        });
    }

    public function catchUpDepreciation(Asset $asset)
    {
        if ($asset->calculation_type !== 'automatic') {
            return;
        }

        $currentDate = now()->startOfDay();

        // Get all unposted depreciations that are due
        $unpostedDepreciations = $asset->depreciations()
            ->where('is_posted', false)
            ->where('period_date', '<=', $currentDate)
            ->orderBy('period_date', 'asc')
            ->get();

        if ($unpostedDepreciations->isEmpty()) {
            return;
        }

        $totalAmount = 0;
        $currentBookValue = $asset->current_book_value ?? $asset->purchase_value;
        $processedIds = [];
        $shiftedDueToClosedPeriod = false;

        DB::transaction(function () use ($asset, $unpostedDepreciations, &$shiftedDueToClosedPeriod) {
            $currentBookValue = $asset->current_book_value ?? $asset->purchase_value;
            $totalAmount = 0;
            $lastDate = null;
            $hasAccountingEffect = $asset->assetCategory ? $asset->assetCategory->has_accounting_effect : true;

            foreach ($unpostedDepreciations as $depreciation) {
                if ($currentBookValue <= $asset->salvage_value) {
                    break;
                }

                $amount = $depreciation->depreciation_amount;
                if (($currentBookValue - $amount) < $asset->salvage_value) {
                    $amount = $currentBookValue - $asset->salvage_value;
                }

                if ($amount <= 0) {
                    break;
                }

                $journalEntryId = null;

                if ($hasAccountingEffect && $asset->depreciation_expense_account_id && $asset->accumulated_depreciation_account_id) {
                    $description = __('lang.asset_depreciation_entry', ['code' => $asset->code, 'month' => $depreciation->month, 'year' => $depreciation->year]) ?? "إهلاك الأصل {$asset->code} لشهر {$depreciation->month} - {$depreciation->year}";

                    $entryDate = $depreciation->period_date->format('Y-m-d');
                    if (!\App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($entryDate)) {
                        $shiftedDueToClosedPeriod = true;
                        
                        $today = now()->format('Y-m-d');
                        if (\App\Models\AccuSoft\FiscalYear::isDateInOpenFiscalYear($today)) {
                            $entryDate = $today;
                        } else {
                            $openYear = \App\Models\AccuSoft\FiscalYear::open()->orderBy('start_date', 'asc')->first();
                            if ($openYear) {
                                if ($openYear->start_date > $depreciation->period_date) {
                                    $entryDate = $openYear->start_date->format('Y-m-d');
                                } else {
                                    $entryDate = $today;
                                }
                            }
                        }
                        $description .= ' (مرحل من فترة مغلقة)';
                    }

                    $journalEntry = $this->journalEntryService->create([
                        'entry_date' => $entryDate,
                        'description' => $description,
                        'entry_type' => \App\Models\AccuSoft\JournalEntry::ENTRY_TYPE_ADJUSTMENT,
                        'status' => \App\Models\AccuSoft\JournalEntry::STATUS_POSTED,
                        'reference_type' => Asset::class,
                        'reference_id' => $asset->id,
                        'details' => [
                            [
                                'tree_account_id' => $asset->depreciation_expense_account_id,
                                'debit' => $amount,
                                'credit' => 0,
                                'description' => 'مصروف إهلاك',
                                'cost_center_id' => $asset->cost_center_id,
                            ],
                            [
                                'tree_account_id' => $asset->accumulated_depreciation_account_id,
                                'debit' => 0,
                                'credit' => $amount,
                                'description' => 'مجمع الإهلاك',
                                'cost_center_id' => $asset->cost_center_id,
                            ]
                        ]
                    ]);
                    $journalEntryId = $journalEntry->id;
                }

                $totalAmount += $amount;
                $currentBookValue -= $amount;
                $lastDate = $depreciation->period_date;

                $depreciation->update([
                    'is_posted' => true,
                    'journal_entry_id' => $journalEntryId,
                    'depreciation_amount' => $amount,
                    'book_value' => $currentBookValue,
                    'accumulated_depreciation' => $asset->total_depreciation + $totalAmount,
                ]);
            }

            if ($totalAmount > 0) {
                $asset->update([
                    'total_depreciation' => $asset->total_depreciation + $totalAmount,
                    'current_book_value' => $currentBookValue,
                    'last_depreciation_date' => $lastDate,
                    'next_depreciation_date' => $lastDate ? $lastDate->copy()->addMonth() : null,
                    'status' => $currentBookValue <= $asset->salvage_value ? Asset::STATUS_FULLY_DEPRECIATED : ($asset->status ?? Asset::STATUS_ACTIVE),
                ]);
            }
        });

        if ($shiftedDueToClosedPeriod && function_exists('flash')) {
            flash()->warning('لا يمكن إنشاء قيد الإهلاك بتاريخه الأصلي لأنه يقع ضمن سنة مالية مغلقة. تم ترحيل القيد إلى أول فترة مالية مفتوحة متاحة.');
        }
    }

    public function generateDepreciationSchedule(Asset $asset): void
    {
        // Don't generate if status is none or calculation type is manual
        if ($asset->depreciation_status == Asset::DEPRECIATION_STATUS_NONE || $asset->calculation_type !== 'automatic') {
            return;
        }

        // Clean up existing unposted schedules
        $asset->depreciations()->where('is_posted', false)->forceDelete();

        if ($asset->useful_life <= 0) return;

        $startDate = Carbon::parse($asset->purchase_date)->startOfMonth();
        
        $currentBookValue = $asset->current_book_value ?? $asset->purchase_value;
        $totalDepreciation = $asset->total_depreciation ?? 0;
        
        $isYearly = $asset->useful_life_type === 'yearly';
        $totalPeriods = $isYearly ? $asset->useful_life : ($asset->useful_life * 12);

        $postedCount = $asset->depreciations()->where('is_posted', true)->count();
        $remainingPeriods = $totalPeriods - $postedCount;
        
        if ($remainingPeriods <= 0) return;

        if ($isYearly) {
            $iteratorDate = $startDate->copy()->addYears($postedCount)->endOfYear();
        } else {
            $iteratorDate = $startDate->copy()->addMonths($postedCount)->endOfMonth();
        }

        // Fixed straight line amount based on prospective application
        $straightLineAmount = 0;
        if ($remainingPeriods > 0 && $asset->depreciation_method == Asset::METHOD_STRAIGHT_LINE) {
            $straightLineAmount = max(0, ($currentBookValue - $asset->salvage_value) / $remainingPeriods);
        }

        for ($i = 0; $i < $remainingPeriods; $i++) {
            if ($asset->depreciation_method == Asset::METHOD_STRAIGHT_LINE) {
                 $amount = $straightLineAmount;
            } elseif ($asset->depreciation_method == Asset::METHOD_DECLINING_BALANCE) {
                $rate = $asset->declining_rate ? ($asset->declining_rate / 100) : (1 / $totalPeriods) * 2;
                $amount = $currentBookValue * $rate;
            } else {
                $amount = 0;
            }
            
            if (($currentBookValue - $amount) < $asset->salvage_value) {
                $amount = $currentBookValue - $asset->salvage_value;
            }
            
            if ($amount <= 0) break; // Reached salvage value

            $currentBookValue -= $amount;
            $totalDepreciation += $amount;

            $asset->depreciations()->create([
                'year' => $iteratorDate->year,
                'month' => $iteratorDate->month,
                'period_date' => $iteratorDate->copy(),
                'depreciation_amount' => $amount,
                'accumulated_depreciation' => $totalDepreciation,
                'book_value' => $currentBookValue,
                'is_posted' => false,
                'entry_type' => 1,
            ]);

            if ($isYearly) {
                $iteratorDate->addYear()->endOfYear();
            } else {
                $iteratorDate->addMonth()->endOfMonth();
            }
        }
    }

    private function getGainLossAccount(float $gainLoss): ?int
    {
        $account = null;
        if ($gainLoss > 0) {
            // Gain -> Search under Other Income (52)
            $parent = \App\Models\AccuSoft\TreeAccounts::whereHas('translations', function ($q) {
                $q->where('name', 'LIKE', '%إيرادات أخرى%')->orWhere('name', 'LIKE', '%Other Income%');
            })->orWhere('code', '52')->first();

            if ($parent) {
                $account = \App\Models\AccuSoft\TreeAccounts::where('parent_id', $parent->id)
                    ->whereHas('translations', function ($q) {
                        $q->where('name', 'LIKE', '%أرباح بيع%')->orWhere('name', 'LIKE', '%Gains from Sale%');
                    })->first();

                if (!$account) {
                    $newCode = \App\Models\AccuSoft\TreeAccounts::generateCode($parent->id);
                    $account = \App\Models\AccuSoft\TreeAccounts::create([
                        'code' => $newCode,
                        'ar' => ['name' => 'أرباح بيع أصول ثابتة'],
                        'en' => ['name' => 'Gains from Sale of Fixed Assets'],
                        'account_type' => $parent->account_type,
                        'parent_id' => $parent->id,
                        'type' => $parent->type,
                        'is_leaf' => 1,
                        'status' => 1,
                    ]);
                }
            }
        } elseif ($gainLoss < 0) {
            // Loss -> Search under General and Administrative Expenses (42) or Other General Expenses (423)
            $parent = \App\Models\AccuSoft\TreeAccounts::whereHas('translations', function ($q) {
                $q->where('name', 'LIKE', '%مصاريف إدارية%')
                  ->orWhere('name', 'LIKE', '%مصاريف عامة%');
            })->orWhere('code', '423')->orWhere('code', '42')
            ->orderBy('level', 'desc')->first();

            if ($parent) {
                $account = \App\Models\AccuSoft\TreeAccounts::where('parent_id', $parent->id)
                    ->whereHas('translations', function ($q) {
                        $q->where('name', 'LIKE', '%خسائر بيع%')->orWhere('name', 'LIKE', '%Losses from Sale%');
                    })->first();

                if (!$account) {
                    $newCode = \App\Models\AccuSoft\TreeAccounts::generateCode($parent->id);
                    $account = \App\Models\AccuSoft\TreeAccounts::create([
                        'code' => $newCode,
                        'ar' => ['name' => 'خسائر بيع أصول ثابتة'],
                        'en' => ['name' => 'Losses from Sale of Fixed Assets'],
                        'account_type' => $parent->account_type,
                        'parent_id' => $parent->id,
                        'type' => $parent->type,
                        'is_leaf' => 1,
                        'status' => 1,
                    ]);
                }
            }
        }

        return $account ? $account->id : null;
    }
}
