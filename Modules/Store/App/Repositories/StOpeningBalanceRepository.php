<?php

namespace Modules\Store\App\Repositories;

use App\Models\BasicDataApp\Product;
use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Modules\Store\App\Models\InventoryAdjustment;
use Modules\Store\App\Models\StOpeningBalance;
use Illuminate\Support\Facades\DB;
use App\Models\StoreApp\StockMovement;
use App\Helpers\StockManagementTrait;
use Mpdf\Tag\Br;

class StOpeningBalanceRepository extends BaseRepository
{
    use StockManagementTrait;

      protected $fieldSearchable = [
        'org_id',
        'branch_id',
        'user_id',
        'document_number',
        'document_date',
        'store_id',
        'status',
        'type',
        'total_items',
        'total_quantity',
        'total_value',
        'approved_by',
        'approved_at',
        'notes',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StOpeningBalance::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'store.openingbalance';

        if (auth()->check()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.user_id', auth()->id());
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.created_by', auth()->id());
            }


        }

        return $query;
    }

    public function statuses(): array
    {
        return StOpeningBalance::statuses();
    }

    public function branches(): array
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function stores(): array
    {
        return Store::ActiveOnly()->get()->pluck('name', 'id')->toArray();
    }

    // public function products()
    // {
    //     return Product::get()->pluck('name', 'id')->toArray();
    // }

    public function header()
    {
        return [
            __('store::models/st_opening_balances.fields.id') ?? 'ID',
            __('store::models/st_opening_balances.fields.user_id') ?? 'User',
            __('store::models/st_opening_balances.fields.document_number') ?? 'Document Number',
            __('store::models/st_opening_balances.fields.document_date') ?? 'Document Date',
            __('store::models/st_opening_balances.fields.store_id') ?? 'Store',
            __('store::models/st_opening_balances.fields.status') ?? 'Status',
            __('store::models/st_opening_balances.fields.total_items') ?? 'Total Items',
            __('store::models/st_opening_balances.fields.total_quantity') ?? 'Total Quantity',
            __('store::models/st_opening_balances.fields.total_value') ?? 'Total Value',
            
        ];
    }

    public function dataExel(): array
    {
        return $this->allQuery()->with(['store', 'user', 'approvedBy'])->get()->map(function ($item) {
            return [
                'id'              => $item->id,
                'user_id'         => $item->user->name ?? $item->user_id,
                'document_number' => $item->document_number,
                'document_date'   => $item->document_date ? \Carbon\Carbon::parse($item->document_date)->format('Y-m-d') : null,
                'store_id'        => $item->store->name ?? $item->store_id,
                'status'          => $item->status_text ?? $item->status,
                'total_items'     => $item->total_items ?? 0,
                'total_quantity'  => $item->total_quantity ?? 0,
                'total_value'     => $item->total_value ?? 0,
            ];
        })->toArray();
    }

    public function name()
    {
        return __('store::models/st_opening_balances.singular') ?? 'Opening Balance';
    }

    public function createOpeningBalance(array $input)
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['user_id'] = auth()->id();
            $input['status'] = $input['status'] ?? StOpeningBalance::STATUS_DRAFT; 
            $input['org_id'] = auth()->user()->org_id ?? null;
            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $openingBalance = parent::create($input);

            $stOpeningBalanceItemRepo = app(StOpeningBalanceItemRepository::class);
            $createdItems = [];
            foreach ($items as $itemData) {
                $createdItem = $stOpeningBalanceItemRepo->create([
                    'opening_balance_id' => $openingBalance->id,
                    'product_id' => $itemData['product_id'],
                    'unit_id' => $itemData['unit_id'],
                    'have_sizes' => $itemData['have_sizes'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['product_units'] ?? $itemData['units'] ?? '[]',
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $itemData['total_cost'],
                    'status' => $openingBalance->status,
                    'notes' => $itemData['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($openingBalance->status == StockMovement::STATUS_APPROVED) {
                foreach ($createdItems as $item) {


                    $this->handleStockMovement($openingBalance, $item);
                }
            }

            if ($openingBalance->status == StockMovement::STATUS_APPROVED) {
                $this->generateJournalEntry($openingBalance);
            }

            return $openingBalance;
        });
    }

    public function updateOpeningBalance(array $input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $openingBalance = parent::find($id);

            if (!$openingBalance->is_editable && ($input['status'] ?? $openingBalance->status) == $openingBalance->status) {
                 throw new \LogicException(__('store::messages.cannot_edit_approved_record') ?? 'Cannot edit records that are not in Draft status.');
            }

            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;
            
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $openingBalance = parent::update($input, $id);

            $stOpeningBalanceItemRepo = app(StOpeningBalanceItemRepository::class);
            $stOpeningBalanceItemRepo->deleteWhere(['opening_balance_id' => $id]);

            $createdItems = [];
            foreach ($items as $itemData) {
                $createdItem = $stOpeningBalanceItemRepo->create([
                    'opening_balance_id' => $openingBalance->id,
                    'product_id' => $itemData['product_id'],
                    'unit_id' => $itemData['unit_id'],
                    'have_sizes' => $itemData['have_sizes'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['product_units'] ?? $itemData['units'] ?? '[]',
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $itemData['total_cost'],
                    'status' => $openingBalance->status,
                    'notes' => $itemData['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($openingBalance->status == StockMovement::STATUS_APPROVED) {
                // تراجع عن جميع الحركات القديمة
                $this->revertAllStockMovements($openingBalance);
                
                // إنشاء حركات جديدة لجميع الأصناف الحالية
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($openingBalance, $item);
                }
            }

            if ($openingBalance->status == StockMovement::STATUS_APPROVED) {
                $this->generateJournalEntry($openingBalance, true);
            }

            return $openingBalance;
        });
    }

    public function deleteOpeningBalance($id)
    {
        return DB::transaction(function () use ($id) {
            $openingBalance = parent::find($id);

            if (!$openingBalance->is_deletable) {
                throw new \LogicException(__('store::messages.cannot_delete_approved_record') ?? 'Cannot delete records that are not in Draft status.');
            }
            if ($openingBalance->journal_entry_id) {
                app(\App\Services\AccuSoft\JournalEntryService::class)->delete(app(\App\Models\AccuSoft\JournalEntry::class)->find($openingBalance->journal_entry_id));
            }
            parent::delete($id);
            return true;
        });
    }

    private function generateJournalEntry($openingBalance, $isUpdate = false)
    {
        $service = app(\Modules\Store\App\Services\InventoryAccountingService::class);

        if ($isUpdate && $openingBalance->journal_entry_id) {
            $oldEntry = app(\App\Models\AccuSoft\JournalEntry::class)->find($openingBalance->journal_entry_id);
            if ($oldEntry) {
                 app(\App\Services\AccuSoft\JournalEntryService::class)->delete($oldEntry);
            }
        }

        $entry = $service->createOpeningBalanceEntry($openingBalance, $openingBalance->total_value);
        if ($entry) {
            $openingBalance->update(['journal_entry_id' => $entry->id]);
        }
    }
}
