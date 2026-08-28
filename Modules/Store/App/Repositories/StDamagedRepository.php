<?php

namespace Modules\Store\App\Repositories;

use App\Models\BasicData\Status;
use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StDamaged;
use Illuminate\Support\Facades\DB;
use App\Models\StoreApp\StockMovement;
use App\Helpers\StockManagementTrait;

class StDamagedRepository extends BaseRepository
{
    use StockManagementTrait;

    protected $fieldSearchable = [
        'document_number',
        'document_date',
        'store_id',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StDamaged::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'store.damaged';

        if (auth()->check()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.user_id', auth()->id());
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.created_by', auth()->id());
            }


        }

        return $query;
    }

    public function statuses()
    {
         return StDamaged::statuses();
    }

    public function branches(): array
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function stores(): array
    {
        return Store::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function header()
    {
        return [
            __('store::models/st_damageds.fields.id') ?? 'ID',
        
            __('store::models/st_damageds.fields.user_id') ?? 'User',
            __('store::models/st_damageds.fields.document_number') ?? 'Document Number',
            __('store::models/st_damageds.fields.document_date') ?? 'Document Date',
            __('store::models/st_damageds.fields.store_id') ?? 'Store',
            __('store::models/st_damageds.fields.status') ?? 'Status',
            __('store::models/st_damageds.fields.total_items') ?? 'Total Items',
            __('store::models/st_damageds.fields.total_quantity') ?? 'Total Quantity',
            __('store::models/st_damageds.fields.total_value') ?? 'Total Value',
         
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
        return __('store::models/st_damageds.singular') ?? 'Damaged Stock';
    }

    public function createDamaged(array $input)
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['user_id'] = auth()->id();
            $input['status'] = $input['status'] ?? StDamaged::STATUS_DRAFT; 
            $input['org_id'] = auth()->user()->org_id ?? null;
            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $damaged = parent::create($input);

            $stDamagedItemRepo = app(StDamagedItemRepository::class);
            $createdItems = [];
            foreach ($items as $item) {
                $createdItem = $stDamagedItemRepo->create([
                    'damaged_id' => $damaged->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'have_sizes' => $item['have_sizes'] ?? 0,
                    'unit' => $item['product_units'] ?? $item['units'] ?? '[]',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'status' => $damaged->status,
                    'notes' => $item['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($damaged->status == StockMovement::STATUS_APPROVED) {
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($damaged, $item);
                }
            }

            if ($damaged->status == StockMovement::STATUS_APPROVED) {
                $this->generateJournalEntry($damaged);
            }

            return $damaged;
        });
    }

    public function updateDamaged(array $input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $damaged = parent::find($id);

            if (!$damaged->is_editable) {
                throw new \LogicException(__('store::messages.cannot_edit_approved_record') ?? 'Cannot edit records that are not in Draft status.');
            }

            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;
            
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $damaged = parent::update($input, $id);

            $stDamagedItemRepo = app(StDamagedItemRepository::class);
            $stDamagedItemRepo->deleteWhere(['damaged_id' => $damaged->id]);

            $createdItems = [];
            foreach ($items as $item) {
                $createdItem = $stDamagedItemRepo->create([
                    'damaged_id' => $damaged->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'have_sizes' => $item['have_sizes'] ?? 0,
                    'unit' => json_encode(json_decode($item['product_units'] ?? ($item['units'] ?? '[]'), true)),
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'status' => $damaged->status,
                    'notes' => $item['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($damaged->status == StockMovement::STATUS_APPROVED) {
                // تراجع عن جميع الحركات القديمة
                $this->revertAllStockMovements($damaged);

                // إنشاء حركات جديدة لجميع الأصناف الحالية
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($damaged, $item);
                }
            }

            if ($damaged->status == StockMovement::STATUS_APPROVED) {
                $this->generateJournalEntry($damaged, true);
            }

            return $damaged;
        });
    }

    public function deleteDamaged($id)
    {
        return DB::transaction(function () use ($id) {
            $damaged = parent::find($id);

            if (!$damaged->is_deletable) {
                throw new \LogicException(__('store::messages.cannot_delete_approved_record') ?? 'Cannot delete records that are not in Draft status.');
            }
            if ($damaged->journal_entry_id) {
                app(\App\Services\AccuSoft\JournalEntryService::class)->delete(app(\App\Models\AccuSoft\JournalEntry::class)->find($damaged->journal_entry_id));
            }
            parent::delete($id);
            // Wait, does StDamaged soft delete? Yes. So deleting is fine.
            return true;
        });
    }

    private function generateJournalEntry($damaged, $isUpdate = false)
    {
        $service = app(\Modules\Store\App\Services\InventoryAccountingService::class);

        if ($isUpdate && $damaged->journal_entry_id) {
            // First we delete the old entry then create a new one (or just create if it updates properly)
            // But InventoryAccountingService createDamagedEntry only creates. We will assume soft delete / recreate or update.
            // For simplicity, if we are updating the damaged items, we should ideally reverse or update the entry.
            // Since JournalEntryService provides update(), let's adapt InventoryAccountingService to also handle update if needed,
            // or simply delete the old entry using deleteDamaged logic and recreate it.
            $oldEntry = app(\App\Models\AccuSoft\JournalEntry::class)->find($damaged->journal_entry_id);
            if ($oldEntry) {
                 app(\App\Services\AccuSoft\JournalEntryService::class)->delete($oldEntry);
            }
        }

        $entry = $service->createDamagedEntry($damaged, $damaged->total_value);
        if ($entry) {
            $damaged->update(['journal_entry_id' => $entry->id]);
        }
    }
}
