<?php

namespace Modules\Store\App\Repositories;

use App\Models\StoreApp\StockMovement;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Store\App\Models\StReceiving;
use Modules\Store\App\Models\StReceivingItem;
use App\Helpers\StockManagementTrait;

class StReceivingRepository extends BaseRepository
{
    use StockManagementTrait;

    protected $fieldSearchable = [
        'org_id', 'branch_id', 'user_id', 'document_number', 'document_date', 'store_id', 'status', 'product_name'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StReceiving::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'store.receiving';

        if (auth()->check()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.user_id', auth()->id());
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.created_by', auth()->id());
            }


        }

        if (isset($search['product_name']) && $search['product_name']) {
            $query->whereHas('items.product', function ($q) use ($search) {
                $q->whereTranslationLike('name', '%' . $search['product_name'] . '%');
            });
        }

        return $query;
    }

    public function statuses(): array
    {
        return StReceiving::statuses();
    }

    public function stores(): array
    {
        return Store::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function header()
    {
        return [
            __('store::models/st_receivings.fields.id') ?? 'ID',
            __('store::models/st_receivings.fields.user_id') ?? 'User',
            __('store::models/st_receivings.fields.document_number') ?? 'Document Number',
            __('store::models/st_receivings.fields.document_date') ?? 'Document Date',
            __('store::models/st_receivings.fields.store_id') ?? 'Store',
            __('store::models/st_receivings.fields.status') ?? 'Status',
            __('store::models/st_receivings.fields.total_items') ?? 'Total Items',
            __('store::models/st_receivings.fields.total_quantity') ?? 'Total Quantity',
            __('store::models/st_receivings.fields.total_value') ?? 'Total Value',
       
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
        return __('store::models/st_receivings.singular') ?? 'Receiving';
    }


    public function createReceiving(array $input)
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['user_id'] = auth()->id() ?? 1;
            $input['status'] = $input['status'] ?? StReceiving::STATUS_DRAFT;
            $input['org_id'] = auth()->user()->org_id ?? 1;
            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $receiving = parent::create($input);

            $stReceivingItemRepo = app(StReceivingItemRepository::class);
            $createdItems = [];
            foreach ($items as $itemData) {
                $createdItem = $stReceivingItemRepo->create([
                    'receiving_id' => $receiving->id,
                    'product_id' => $itemData['product_id'],
                    'unit_id' => $itemData['unit_id'],
                    'have_sizes' => $itemData['have_sizes'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['product_units'] ?? '[]',
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $itemData['total_cost'],
                    'status' => $receiving->status,
                    'notes' => $itemData['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($receiving->status == StReceiving::STATUS_APPROVED) {
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($receiving, $item);
                }
                $this->generateJournalEntry($receiving);
            }

            return $receiving;
        });
    }

    public function updateReceiving(array $input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $receiving = parent::find($id);
            if (!$receiving->is_editable && ($input['status'] ?? $receiving->status) == $receiving->status) {
                 throw new \LogicException(__('store::messages.cannot_edit_approved_record') ?? 'Cannot edit approved records.');
            }

            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $receiving = parent::update($input, $id);

            $stReceivingItemRepo = app(StReceivingItemRepository::class);
            $stReceivingItemRepo->deleteWhere(['receiving_id' => $id]);

            $createdItems = [];
            foreach ($items as $itemData) {
                $createdItem = $stReceivingItemRepo->create([
                    'receiving_id' => $receiving->id,
                    'product_id' => $itemData['product_id'],
                    'unit_id' => $itemData['unit_id'],
                    'have_sizes' => $itemData['have_sizes'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['product_units'] ?? '[]',
                    'unit_cost' => $itemData['unit_cost'],
                    'total_cost' => $itemData['total_cost'],
                    'status' => $receiving->status,
                    'notes' => $itemData['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($receiving->status == StReceiving::STATUS_APPROVED) {
                $this->revertAllStockMovements($receiving);
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($receiving, $item);
                }
                $this->generateJournalEntry($receiving, true);
            }

            return $receiving;
        });
    }

    private function generateJournalEntry($receiving, $isUpdate = false)
    {
        $service = app(\Modules\Store\App\Services\InventoryAccountingService::class);
        if ($isUpdate && $receiving->journal_entry_id) {
            $oldEntry = app(\App\Models\AccuSoft\JournalEntry::class)->find($receiving->journal_entry_id);
            if ($oldEntry) app(\App\Services\AccuSoft\JournalEntryService::class)->delete($oldEntry);
        }
        $entry = $service->createReceivingEntry($receiving, $receiving->total_value);
        if ($entry) $receiving->update(['journal_entry_id' => $entry->id]);
    }
}
