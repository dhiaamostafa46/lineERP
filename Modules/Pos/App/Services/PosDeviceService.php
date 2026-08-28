<?php

namespace Modules\Pos\App\Services;

use Exception;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\StoreApp\Store;
use Illuminate\Support\Facades\DB;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\AccuSoft\AccountMapping;
use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Models\PosPaymentMethod;
use Modules\Pos\App\Repositories\PosDeviceRepository;

class PosDeviceService
{
    private $posDeviceRepository;

    public function __construct(PosDeviceRepository $posDeviceRepository)
    {
        $this->posDeviceRepository = $posDeviceRepository;
    }

    public function ensureDefaultPosAccountsExist(): void
    {
        $treasuryParent = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_TREASURY)
            ->where('is_leaf', 0)
            ->orderBy('level', 'asc')
            ->first();

        $parentId = $treasuryParent ? $treasuryParent->id : null;

        if ($parentId) {
            $posSafe = TreeAccounts::whereHas('translations', function ($q) {
                $q->where('name', 'like', '%نقطة البيع%');
            })->first();
            if (!$posSafe) {
                $posSafe = new TreeAccounts();
                $posSafe->fill([
                    'ar' => ['name' => 'صندوق نقطة البيع'],
                    'en' => ['name' => 'POS Safe']
                ]);
                $posSafe->account_type = TreeAccounts::ACCOUNT_TYPE_TREASURY;
                $posSafe->parent_id = $parentId;
                $posSafe->code = (TreeAccounts::where('parent_id', $parentId)->max('code') ?? 111011) + 1;
                $posSafe->status = 1;
                $posSafe->is_leaf = 1;
                $posSafe->save();
            }

            $cashDrop = TreeAccounts::whereHas('translations', function ($q) {
                $q->where('name', 'like', '%سحوبات%');
            })->first();
            if (!$cashDrop) {
                $cashDrop = new TreeAccounts();
                $cashDrop->fill([
                    'ar' => ['name' => 'صندوق السحوبات والعهد'],
                    'en' => ['name' => 'Withdrawals and Petty Cash Safe']
                ]);
                $cashDrop->account_type = TreeAccounts::ACCOUNT_TYPE_TREASURY;
                $cashDrop->parent_id = $parentId;
                $cashDrop->code = (TreeAccounts::where('parent_id', $parentId)->max('code') ?? 111011) + 1;
                $cashDrop->status = 1;
                $cashDrop->is_leaf = 1;
                $cashDrop->save();
            }
        }
    }

    /**
     * Get lookup data for PosDevice forms (create/edit).
     */
    public function getFormData(): array
    {
        $this->ensureDefaultPosAccountsExist();

        // Not using Caching as requested by user
        $data['all_stores'] = Store::with('translations')->get()->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'branch_id' => $store->branch_id,
            ];
        })->toArray();
        $data['stores'] = Store::get()->pluck('name', 'id')->toArray();
        $data['branches'] = Branch::get()->pluck('name', 'id')->toArray();
        $data['accounts'] = TreeAccounts::where('status', 1)->where('is_leaf', 1)->get()->pluck('name', 'id')->toArray();
        
        $posSafe = TreeAccounts::whereHas('translations', function ($q) {
            $q->where('name', 'like', '%نقطة البيع%');
        })->first();
        $cashDrop = TreeAccounts::whereHas('translations', function ($q) {
            $q->where('name', 'like', '%سحوبات%');
        })->first();

        $data['default_accounts'] = [
            'sales_account_id' => $this->getMappedOrTypeAccount('sales', TreeAccounts::ACCOUNT_TYPE_SALES),
            'discount_account_id' => $this->getMappedOrTypeAccount('sales_discount', TreeAccounts::ACCOUNT_TYPE_EXPENSE),
            'shortage_account_id' => $this->getMappedOrTypeAccount('inventory_adjustment_loss', TreeAccounts::ACCOUNT_TYPE_EXPENSE),
            'overage_account_id' => $this->getMappedOrTypeAccount('inventory_adjustment_profit', TreeAccounts::ACCOUNT_TYPE_REVENUE),
            'vat_account_id' => $this->getMappedOrTypeAccount('sales_tax', TreeAccounts::ACCOUNT_TYPE_LIABILITY),
            'cogs_account_id' => $this->getMappedOrTypeAccount('cogs', TreeAccounts::ACCOUNT_TYPE_COST_OF_SALES),
            'bank_account_id' => $this->getMappedOrTypeAccount('bank', TreeAccounts::ACCOUNT_TYPE_BANK),
            'cash_account_id' => $posSafe ? $posSafe->id : $this->getMappedOrTypeAccount('cash', TreeAccounts::ACCOUNT_TYPE_TREASURY),
            'customer_account_id' => $this->getMappedOrTypeAccount('customer', TreeAccounts::ACCOUNT_TYPE_CUSTOMERS),
            'main_safe_account_id' => $this->getMappedOrTypeAccount('safe', TreeAccounts::ACCOUNT_TYPE_TREASURY),
            'expense_account_id' => $cashDrop ? $cashDrop->id : $this->getMappedOrTypeAccount('expense', TreeAccounts::ACCOUNT_TYPE_EXPENSE),
        ];

        return $data;
    }

    /**
     * Create a new PosDevice with all related business logic.
     */
    public function createDevice(array $input, Request $request): \Illuminate\Database\Eloquent\Model
    {
        $branchId = $input['branch_id'] ?? null;
        $this->validateStoreExistence($branchId);

        return DB::transaction(function () use ($input, $request) {
            // Handle linked_users
            if (!isset($input['is_users_linked']) || $input['is_users_linked'] == 0) {
                $input['is_users_linked'] = false;
                $input['linked_users'] = null;
            }

            $input = $this->prepareBooleans($input);

            // Set account IDs from request directly
            $input['main_safe_account_id'] = $request->input('main_safe_account_id');
            $input['shortage_account_id'] = $request->input('shortage_account_id');
            $input['overage_account_id'] = $request->input('overage_account_id');

            // Create device via Repository
            $device = $this->posDeviceRepository->create($input);

            // Create 5 fixed payment methods for the new device
            $paymentTypes = PosPaymentMethod::types();

            foreach ($paymentTypes as $type => $name) {
                $accountId = $request->input("payment_accounts.{$type}");
                $isActive = isset($request->payment_active[$type]) && $request->payment_active[$type] == 1;
                
                if (in_array($type, [PosPaymentMethod::TYPE_CREDIT, PosPaymentMethod::TYPE_INSTALLMENT])) {
                    $accountId = null;
                }

                PosPaymentMethod::create([
                    'device_id' => $device->id,
                    'name' => $name,
                    'type' => $type,
                    'account_id' => $accountId,
                    'is_active' => $isActive,
                    'is_default' => $type === PosPaymentMethod::TYPE_CASH
                ]);
            }

            return $device;
        });
    }

    /**
     * Update an existing PosDevice with related business logic.
     */
    public function updateDevice(int $id, array $input, Request $request)
    {
        return DB::transaction(function () use ($id, $input, $request) {
            // Handle linked_users
            if (!isset($input['is_users_linked']) || $input['is_users_linked'] == 0) {
                $input['is_users_linked'] = false;
                $input['linked_users'] = null;
            }

            $input = $this->prepareBooleans($input);

            // Update account IDs directly from request
            $input['main_safe_account_id'] = $request->input('main_safe_account_id');
            $input['expense_account_id'] = $request->input('expense_account_id');
            $input['shortage_account_id'] = $request->input('shortage_account_id');
            $input['overage_account_id'] = $request->input('overage_account_id');

            // Update device via Repository
            $device = $this->posDeviceRepository->update($input, $id);

            // Update payment methods
            $paymentTypes = PosPaymentMethod::types();
            foreach ($paymentTypes as $type => $name) {
                $isActive = isset($request->payment_active[$type]) && $request->payment_active[$type] == 1;
                $accountId = $request->input("payment_accounts.{$type}");
                
                if (in_array($type, [PosPaymentMethod::TYPE_CREDIT, PosPaymentMethod::TYPE_INSTALLMENT])) {
                    $accountId = null;
                }
                
                PosPaymentMethod::where('device_id', $device->id)
                    ->where('type', $type)
                    ->update([
                        'account_id' => $accountId,
                        'is_active' => $isActive
                    ]);
            }

            return $device;
        });
    }

    /**
     * Ensure backward compatibility for old devices without payment methods.
     */
    public function ensurePaymentMethodsExist(PosDevice $device)
    {
        $existingTypes = $device->paymentMethods->pluck('type')->toArray();
        foreach (PosPaymentMethod::types() as $type => $name) {
            if (!in_array($type, $existingTypes)) {
                PosPaymentMethod::create([
                    'device_id' => $device->id,
                    'name' => $name,
                    'type' => $type,
                    'account_id' => null,
                    'is_active' => true,
                    'is_default' => $type === PosPaymentMethod::TYPE_CASH
                ]);
            }
        }
    }

    public function getMappedOrTypeAccount(string $mappingKey, int $accountType): ?int
    {
        return AccountMapping::getAccountId($mappingKey) 
            ?? (TreeAccounts::where('account_type', $accountType)->where('status', 1)->where('is_leaf', 1)->first()->id ?? null);
    }

    private function validateStoreExistence($branchId)
    {
        if ($branchId && Store::where('branch_id', $branchId)->count() === 0) {
            throw new Exception('لا يوجد أي مستودع مرتبط بهذا الفرع. يرجى إنشاء مستودع وربطه بالفرع قبل حفظ جهاز نقطة البيع.');
        }
    }

    private function prepareBooleans(array $input): array
    {
        $booleans = [
            'is_active', 'auto_journal_entry', 'allow_negative_stock', 'auto_print_receipt', 
            'auto_open_drawer', 'allow_price_modification', 'allow_discount_modification', 
            'show_available_qty', 'enable_pos_returns', 'enable_cash_movements'
        ];

        foreach ($booleans as $field) {
            if (isset($input[$field])) {
                $input[$field] = filter_var($input[$field], FILTER_VALIDATE_BOOLEAN) || $input[$field] == '1' || $input[$field] == 'on';
            } else {
                $input[$field] = false;
            }
        }

        return $input;
    }


}