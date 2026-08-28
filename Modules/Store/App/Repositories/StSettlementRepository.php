<?php

namespace Modules\Store\App\Repositories;

use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StSettlement;
use Illuminate\Support\Facades\DB;
use App\Models\StoreApp\StockMovement;
use App\Helpers\StockManagementTrait;

class StSettlementRepository extends BaseRepository
{
    use StockManagementTrait;

    protected $fieldSearchable = [
        'id', 'document_number', 'document_date', 'store_id', 'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StSettlement::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'store.settlement';

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
         return StSettlement::statuses();
    }

    public function stores(): array
    {
        return Store::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function header()
    {
        return [
            __('store::models/st_settlements.fields.id') ?? 'ID',
            __('store::models/st_settlements.fields.user_id') ?? 'User',
            __('store::models/st_settlements.fields.document_number') ?? 'Document Number',
            __('store::models/st_settlements.fields.document_date') ?? 'Document Date',
            __('store::models/st_settlements.fields.store_id') ?? 'Store',
            __('store::models/st_settlements.fields.status') ?? 'Status',
            __('store::models/st_settlements.fields.total_items') ?? 'Total Items',
            __('store::models/st_settlements.fields.total_quantity') ?? 'Total Quantity',
            __('store::models/st_settlements.fields.total_value') ?? 'Total Value',
            
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
        return __('store::models/st_settlements.singular') ?? 'Settlement';
    }

    public function createSettlement(array $input)
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['user_id'] = auth()->id();
            $input['status'] = $input['status'] ?? StSettlement::STATUS_DRAFT; 
            $input['org_id'] = auth()->user()->org_id ?? null;
            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('variance_quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            // Regenerate document number to avoid "already taken" error if form was stale
            $input['document_number'] = StSettlement::generateDocumentNumber();

            $settlement = parent::create($input);

            $stSettlementItemRepo = app(StSettlementItemRepository::class);
            $createdItems = [];
            foreach ($items as $item) {
                // If actual > system => Surplus (in)
                // If actual < system => Shortage (out)
                $sys_qty = $item['system_quantity'] ?? 0;
                $act_qty = $item['actual_quantity'] ?? 0;
                $variance = $act_qty - $sys_qty;
                $varianceType = $variance >= 0 ? 'in' : 'out';

                $createdItem = $stSettlementItemRepo->create([
                    'settlement_id' => $settlement->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'have_sizes' => $item['have_sizes'] ?? 0,
                    'unit' => $item['product_units'] ?? $item['units'] ?? '[]',
                    'system_quantity' => $sys_qty,
                    'actual_quantity' => $act_qty,
                    'variance_quantity' => abs($variance),
                    'variance_type' => $varianceType,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => abs($variance) * $item['unit_cost'],
                    'status' => $settlement->status,
                    'notes' => $item['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($settlement->status == StSettlement::STATUS_APPROVED) {
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($settlement, $item, 'variance_quantity');
                }
            }

            if ($settlement->status == StSettlement::STATUS_APPROVED) {
                $this->generateJournalEntry($settlement, $createdItems);
            }

            return $settlement;
        });
    }

    public function updateSettlement(array $input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $settlement = parent::find($id);

            if (!$settlement->is_editable) {
                throw new \LogicException(__('store::messages.cannot_edit_approved_record') ?? 'Cannot edit records that are cancelled.');
            }

            $wasApproved = ($settlement->status == StSettlement::STATUS_APPROVED);

            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;
            
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('variance_quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            if ($wasApproved) {
                $this->revertAllStockMovements($settlement);
            }

            $settlement = parent::update($input, $id);

            $stSettlementItemRepo = app(StSettlementItemRepository::class);
            $stSettlementItemRepo->deleteWhere(['settlement_id' => $settlement->id]);

            $createdItems = [];
            foreach ($items as $item) {
                $sys_qty = $item['system_quantity'] ?? 0;
                $act_qty = $item['actual_quantity'] ?? 0;
                $variance = $act_qty - $sys_qty;
                $varianceType = $variance >= 0 ? 'in' : 'out';

                $createdItem = $stSettlementItemRepo->create([
                    'settlement_id' => $settlement->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'have_sizes' => $item['have_sizes'] ?? 0,
                    'unit' => $item['product_units'] ?? $item['units'] ?? '[]',
                    'system_quantity' => $sys_qty,
                    'actual_quantity' => $act_qty,
                    'variance_quantity' => abs($variance),
                    'variance_type' => $varianceType,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => abs($variance) * $item['unit_cost'],
                    'status' => $settlement->status,
                    'notes' => $item['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($settlement->status == StSettlement::STATUS_APPROVED) {
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($settlement, $item, 'variance_quantity');
                }
                $this->generateJournalEntry($settlement, $createdItems, true);
            }

            return $settlement;
        });
    }

    public function deleteSettlement($id)
    {
        return DB::transaction(function () use ($id) {
            $settlement = parent::find($id);

            if (!$settlement->is_deletable) {
                throw new \LogicException(__('store::messages.cannot_delete_approved_record') ?? 'Cannot delete records that are cancelled.');
            }

            if ($settlement->status == StSettlement::STATUS_APPROVED) {
                $this->revertAllStockMovements($settlement);
            }

            if ($settlement->journal_entry_id) {
                $entry = app(\App\Models\AccuSoft\JournalEntry::class)->find($settlement->journal_entry_id);
                if ($entry) {
                    app(\App\Services\AccuSoft\JournalEntryService::class)->delete($entry, true);
                }
            }

            $settlement->items()->delete();
            parent::delete($id);
            return true;
        });
    }

    public function authorizeSettlement($id)
    {
        return DB::transaction(function () use ($id) {
            $settlement = parent::find($id);
            if ($settlement->status != StSettlement::STATUS_DRAFT) {
                throw new \LogicException('يمكن تعميد السندات في حالة المسودة فقط.');
            }

            $settlement->update(['status' => StSettlement::STATUS_APPROVED]);
            $items = $settlement->items;

            foreach ($items as $item) {
                $item->update(['status' => StSettlement::STATUS_APPROVED]);
                $this->handleStockMovement($settlement, $item, 'variance_quantity');
            }

            $this->generateJournalEntry($settlement, $items);

            return $settlement;
        });
    }

    private function generateJournalEntry($settlement, $items, $isUpdate = false)
    {
        $service = app(\Modules\Store\App\Services\InventoryAccountingService::class);

        $totalShortageValue = collect($items)->where('variance_type', 'out')->sum('total_cost');
        $totalSurplusValue = collect($items)->where('variance_type', 'in')->sum('total_cost');

        if ($isUpdate && $settlement->journal_entry_id) {
            $entry = $service->updateSettlementEntry($settlement, $totalSurplusValue, $totalShortageValue);
        } else {
            $entry = $service->createSettlementEntry($settlement, $totalSurplusValue, $totalShortageValue);
        }

        if ($entry && $settlement->journal_entry_id != $entry->id) {
            $settlement->update(['journal_entry_id' => $entry->id]);
        }
    }
}
