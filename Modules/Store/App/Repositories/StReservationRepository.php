<?php

namespace Modules\Store\App\Repositories;

use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StReservation;
use Modules\Store\App\Models\StReservationItem;
use Illuminate\Support\Facades\DB;
use App\Models\StoreApp\StockMovement;
use App\Helpers\StockManagementTrait;

class StReservationRepository extends BaseRepository
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
        return StReservation::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'store.reservation';

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
         return StReservation::statuses();
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
            __('store::models/st_reservations.fields.id') ?? 'ID',
            __('store::models/st_reservations.fields.user_id') ?? 'User',
            __('store::models/st_reservations.fields.document_number') ?? 'Document Number',
            __('store::models/st_reservations.fields.document_date') ?? 'Document Date',
            __('store::models/st_reservations.fields.store_id') ?? 'Store',
            __('store::models/st_reservations.fields.status') ?? 'Status',
            __('store::models/st_reservations.fields.total_items') ?? 'Total Items',
            __('store::models/st_reservations.fields.total_quantity') ?? 'Total Quantity',
            __('store::models/st_reservations.fields.total_value') ?? 'Total Value',
       
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
        return __('store::models/st_reservations.singular') ?? 'Reservation';
    }

    public function createReservation(array $input)
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['user_id'] = auth()->id();
            $input['status'] = $input['status'] ?? StReservation::STATUS_DRAFT; 
            $input['org_id'] = auth()->user()->org_id ?? null;
            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');
            
            $input['document_number'] = StReservation::generateDocumentNumber();

            $reservation = parent::create($input);

            $stReservationItemRepo = app(StReservationItemRepository::class);
            $createdItems = [];
            foreach ($items as $item) {
                $createdItem = $stReservationItemRepo->create([
                    'reservation_id' => $reservation->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'have_sizes' => $item['have_sizes'] ?? 0,
                    'unit' => $item['product_units'] ?? $item['units'] ?? '[]',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'status' => $reservation->status,
                    'notes' => $item['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($reservation->status == StReservation::STATUS_APPROVED) {
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($reservation, $item);
                }
                $reservation->update([
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            }

            return $reservation;
        });
    }

    public function updateReservation(array $input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $reservation = parent::find($id);

            if (!$reservation->is_editable) {
                throw new \LogicException(__('store::messages.cannot_edit_approved_record') ?? 'Cannot edit records that are not in Draft status.');
            }

            $input['branch_id'] = Store::findOrFail($input['store_id'])->branch_id;
            
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['total_items'] = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value'] = collect($items)->sum('total_cost');

            $reservation = parent::update($input, $id);

            $stReservationItemRepo = app(StReservationItemRepository::class);
            $stReservationItemRepo->deleteWhere(['reservation_id' => $reservation->id]);

            $createdItems = [];
            foreach ($items as $item) {
                $createdItem = $stReservationItemRepo->create([
                    'reservation_id' => $reservation->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'have_sizes' => $item['have_sizes'] ?? 0,
                    'unit' => $item['product_units'] ?? $item['units'] ?? '[]',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'status' => $reservation->status,
                    'notes' => $item['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($reservation->status == StReservation::STATUS_APPROVED) {
                $this->revertAllStockMovements($reservation);
                foreach ($createdItems as $item) {
                    $this->handleStockMovement($reservation, $item);
                }
                $reservation->update([
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            }

            return $reservation;
        });
    }

    public function authorizeReservation($id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = parent::find($id);
            if ($reservation->status != StReservation::STATUS_DRAFT) {
                throw new \LogicException('Only draft reservations can be authorized.');
            }

            $reservation->update([
                'status' => StReservation::STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            foreach ($reservation->items as $item) {
                $this->handleStockMovement($reservation, $item);
            }

            return $reservation;
        });
    }

    public function returnToWarehouse($id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = parent::find($id);
            if ($reservation->status != StReservation::STATUS_APPROVED) {
                throw new \LogicException('Only approved reservations can be returned.');
            }

            $reservation->update([
                'status' => StReservation::STATUS_RETURNED,
                'returned_by' => auth()->id(),
                'returned_at' => now(),
            ]);

            // This will trigger 'in' direction because status is RETURNED in getDocumentConfig
            foreach ($reservation->items as $item) {
                $this->handleStockMovement($reservation, $item);
            }

            return $reservation;
        });
    }

    public function deleteReservation($id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = parent::find($id);
            if (!$reservation->is_deletable) {
                throw new \LogicException(__('store::messages.cannot_delete_approved_record') ?? 'Cannot delete records that are not in Draft status.');
            }
            parent::delete($id);
            return true;
        });
    }
}
