<?php

namespace Modules\Store\App\Repositories;

use App\Models\StoreApp\StockMovement;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Store\App\Models\StDirectTransfer;
use Modules\Store\App\Models\StDirectTransferItem;
use App\Helpers\StockManagementTrait;
use App\Models\AccuSoft\JournalEntry;

class StDirectTransferRepository extends BaseRepository
{
    use StockManagementTrait;
    protected $accountingService;

    public function __construct(\Illuminate\Container\Container $app, \Modules\Store\App\Services\InventoryAccountingService $accountingService)
    {
        parent::__construct($app);
        $this->accountingService = $accountingService;
    }

    protected $fieldSearchable = [
        'org_id',
        'branch_id',
        'user_id',
        'document_number',
        'document_date',
        'from_store_id',
        'to_store_id',
        'status',
        'product_name'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StDirectTransfer::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'store.direct_transfer';

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

    public function statuses($transfer_type = null, $currentStatus = null): array
    {
        $all = StDirectTransfer::statuses();

        if ($transfer_type == StDirectTransfer::TYPE_DIRECT) {
            return [
                StDirectTransfer::STATUS_DRAFT          => $all[StDirectTransfer::STATUS_DRAFT],
                StDirectTransfer::STATUS_SOURCE_APPROVED => __('store::models/st_direct_transfers.status.transferred'),
            ];
        }

        if ($transfer_type == StDirectTransfer::TYPE_INDIRECT) {
            if (is_null($currentStatus) || $currentStatus == StDirectTransfer::STATUS_DRAFT) {
                return [
                    StDirectTransfer::STATUS_DRAFT          => $all[StDirectTransfer::STATUS_DRAFT],
                    StDirectTransfer::STATUS_SOURCE_APPROVED => $all[StDirectTransfer::STATUS_SOURCE_APPROVED],
                ];
            }

            if (in_array($currentStatus, [
                StDirectTransfer::STATUS_SOURCE_APPROVED,
                StDirectTransfer::STATUS_DESTINATION_DRAFT,
                StDirectTransfer::STATUS_PARTIAL_APPROVED,
            ])) {
                return [
                    StDirectTransfer::STATUS_DESTINATION_DRAFT    => $all[StDirectTransfer::STATUS_DESTINATION_DRAFT],
                    StDirectTransfer::STATUS_PARTIAL_APPROVED     => $all[StDirectTransfer::STATUS_PARTIAL_APPROVED],
                    StDirectTransfer::STATUS_DESTINATION_APPROVED => $all[StDirectTransfer::STATUS_DESTINATION_APPROVED],
                ];
            }
        }

        return $all;
    }

    public function stores(): array
    {
        return Store::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function header()
    {
        return [
            __('store::models/st_direct_transfers.fields.id') ?? 'ID',
        
            __('store::models/st_direct_transfers.fields.user_id') ?? 'User',
            __('store::models/st_direct_transfers.fields.document_number') ?? 'Document Number',
            __('store::models/st_direct_transfers.fields.document_date') ?? 'Document Date',
            __('store::models/st_direct_transfers.fields.from_store_id') ?? 'From Store',
            __('store::models/st_direct_transfers.fields.to_store_id') ?? 'To Store',
            __('store::models/st_direct_transfers.fields.status') ?? 'Status',
            __('store::models/st_direct_transfers.fields.total_items') ?? 'Total Items',
            __('store::models/st_direct_transfers.fields.total_quantity') ?? 'Total Quantity',
            __('store::models/st_direct_transfers.fields.total_value') ?? 'Total Value',
         
            
        ];
    }

    public function dataExel(): array
    {
        return $this->allQuery()->with(['fromStore', 'toStore', 'user', 'approvedBy'])->get()->map(function ($item) {
            return [
                'id'              => $item->id,
           
                'user_id'         => $item->user->name ?? $item->user_id,
                'document_number' => $item->document_number,
                'document_date'   => $item->document_date ? \Carbon\Carbon::parse($item->document_date)->format('Y-m-d') : null,
                'from_store_id'   => $item->fromStore->name ?? $item->from_store_id,
                'to_store_id'     => $item->toStore->name ?? $item->to_store_id,
                'status'          => $item->status_text ?? $item->status,
                'total_items'     => $item->total_items ?? 0,
                'total_quantity'  => $item->total_quantity ?? 0,
                'total_value'     => $item->total_value ?? 0,
            ];
        })->toArray();
    }

    public function name()
    {
        return __('store::models/st_direct_transfers.singular') ?? 'Direct Transfer';
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    public function createTransfer(array $input)
    {
        if ($input['from_store_id'] == $input['to_store_id']) {
            throw new \LogicException('لا يمكن التحويل لنفس المستودع. يرجى اختيار مستودع مختلف.');
        }

        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            unset($input['items']);

            $input['user_id']   = auth()->id() ?? 1;
            $input['status']    = $input['status'] ?? StDirectTransfer::STATUS_DRAFT;
            $input['org_id']    = auth()->user()->org_id ?? 1;
            $input['branch_id'] = Store::findOrFail($input['from_store_id'])->branch_id;

            $input['total_items']    = count($items);
            $input['total_quantity'] = collect($items)->sum('quantity');
            $input['total_value']    = collect($items)->sum('total_cost');

            $transfer = parent::create($input);

            $stItemRepo  = app(StDirectTransferItemRepository::class);
            $createdItems = [];
            foreach ($items as $itemData) {
                $qty = (isset($itemData['quantity']) && $itemData['quantity'] !== '' && (float)$itemData['quantity'] > 0) ? (float)$itemData['quantity'] : 1;
                $cost = (isset($itemData['unit_cost']) && $itemData['unit_cost'] !== '') ? (float)$itemData['unit_cost'] : 0;
                $totalCost = (isset($itemData['total_cost']) && $itemData['total_cost'] !== '') ? (float)$itemData['total_cost'] : ($qty * $cost);

                $createdItem = $stItemRepo->create([
                    'direct_transfer_id' => $transfer->id,
                    'product_id'         => $itemData['product_id'],
                    'unit_id'            => $itemData['unit_id'],
                    'have_sizes'         => $itemData['have_sizes'] ?? 0,
                    'quantity'           => $qty,
                    'received_quantity'  => $itemData['received_quantity'] ?? $qty,
                    'variance_quantity'  => $itemData['variance_quantity'] ?? 0,
                    'unit'               => $itemData['product_units'] ?? '[]',
                    'unit_cost'          => $cost,
                    'total_cost'         => $totalCost,
                    'status'             => $transfer->status,
                    'notes'              => $itemData['notes'] ?? null,
                ]);
                $createdItems[] = $createdItem;
            }

            if ($input['status'] == StDirectTransfer::STATUS_SOURCE_APPROVED) {
                if ($transfer->is_direct) {
                    $this->createMovementForItems($transfer, $createdItems, 'quantity', 'TRF-OUT', 'out', $transfer->from_store_id);
                    $this->createMovementForItems($transfer, $createdItems, 'quantity', 'TRF-IN', 'in', $transfer->to_store_id);
                    $this->generateDirectJournalEntry($transfer);
                    $transfer->update(['status' => StDirectTransfer::STATUS_DESTINATION_APPROVED]);
                } else {
                    $this->createMovementForItems($transfer, $createdItems, 'quantity', 'TRF-OUT', 'out', $transfer->from_store_id);
                    $this->generateSourceJournalEntry($transfer);
                }
            }

            return $transfer;
        });
    }

    // =========================================================================
    // UPDATE (Draft editing & Source Approval only)
    // =========================================================================

    public function updateTransfer(array $input, $id)
    {
        if (isset($input['from_store_id'], $input['to_store_id']) && $input['from_store_id'] == $input['to_store_id']) {
            throw new \LogicException(__('store::messages.same_store_error'));
        }

        return DB::transaction(function () use ($input, $id) {
            /** @var StDirectTransfer $transfer */
            $transfer  = parent::find($id);
            $oldStatus = $transfer->status;
            $newStatus = (int)($input['status'] ?? $oldStatus);

            $itemsData = $input['items'] ?? [];
            unset($input['items']);

            if ($oldStatus == StDirectTransfer::STATUS_DRAFT) {
                $input['total_items']    = count($itemsData);
                $input['total_quantity'] = collect($itemsData)->sum('quantity');
                $input['total_value']    = collect($itemsData)->sum('total_cost');
                $transfer = parent::update($input, $id);
            } else {
                $updateData = ['status' => $newStatus];
                if (isset($input['notes'])) $updateData['notes'] = $input['notes'];
                $transfer = parent::update($updateData, $id);
            }

            $createdItems = [];
            if (empty($itemsData)) {
                $createdItems = $transfer->items;
            } else {
                foreach ($itemsData as $itemData) {
                    $qty      = (float)$itemData['quantity'];
                    $recv     = (float)($itemData['received_quantity'] ?? 0);
                    $variance = $recv - $qty;

                    $item = $transfer->items()
                        ->where('product_id', $itemData['product_id'])
                        ->where('have_sizes', $itemData['have_sizes'] ?? 0)
                        ->first();

                    if ($item) {
                        if ($oldStatus == StDirectTransfer::STATUS_DRAFT) {
                            $item->update([
                                'quantity'          => $qty,
                                'received_quantity' => $recv,
                                'variance_quantity' => $variance,
                                'unit_cost'         => $itemData['unit_cost'],
                                'total_cost'        => $itemData['total_cost'],
                                'notes'             => $itemData['notes'] ?? null,
                            ]);
                        } else {
                            $item->update([
                                'received_quantity' => $recv,
                                'variance_quantity' => $variance,
                                'notes'             => $itemData['notes'] ?? null,
                            ]);
                        }
                    } else {
                        $stItemRepo = app(StDirectTransferItemRepository::class);
                        $item = $stItemRepo->create([
                            'direct_transfer_id' => $transfer->id,
                            'product_id'         => $itemData['product_id'],
                            'unit_id'            => $itemData['unit_id'],
                            'have_sizes'         => $itemData['have_sizes'] ?? 0,
                            'quantity'           => $qty,
                            'received_quantity'  => $recv,
                            'variance_quantity'  => $variance,
                            'unit'               => $itemData['product_units'] ?? '[]',
                            'unit_cost'          => $itemData['unit_cost'],
                            'total_cost'         => $itemData['total_cost'],
                            'status'             => $transfer->status,
                        ]);
                    }

                    $item->current_received = (float)($itemData['current_received'] ?? 0);
                    $createdItems[] = $item;
                }
            }

            // 1. Destination Draft: save only
            if ($newStatus == StDirectTransfer::STATUS_DESTINATION_DRAFT) {
                return $transfer;
            }

            // 2. Source Approval (Draft → In Transit / Direct Complete)
            if ($newStatus == StDirectTransfer::STATUS_SOURCE_APPROVED && $oldStatus == StDirectTransfer::STATUS_DRAFT) {
                if ($transfer->is_direct) {
                    $this->createMovementForItems($transfer, $createdItems, 'quantity', 'TRF-OUT', 'out', $transfer->from_store_id);
                    $this->createMovementForItems($transfer, $createdItems, 'quantity', 'TRF-IN', 'in', $transfer->to_store_id);
                    $this->generateDirectJournalEntry($transfer);
                    $transfer->update(['status' => StDirectTransfer::STATUS_DESTINATION_APPROVED]);
                } else {
                    $this->createMovementForItems($transfer, $createdItems, 'quantity', 'TRF-OUT', 'out', $transfer->from_store_id);
                    $this->generateSourceJournalEntry($transfer);
                }
                return $transfer;
            }

            // 3. Destination Receipt (5) — delegates to validateTransfer logic
            if ($newStatus == StDirectTransfer::STATUS_DESTINATION_APPROVED) {
                $itemsForTotals  = empty($itemsData) ? $transfer->items : $createdItems;
                $totalQty        = collect($itemsForTotals)->sum('quantity');
                $totalReceived   = collect($itemsForTotals)->sum('received_quantity');

                $finalStatus = ($totalQty == $totalReceived)
                    ? StDirectTransfer::STATUS_DESTINATION_APPROVED
                    : StDirectTransfer::STATUS_PARTIAL_APPROVED;

                $transfer->update(['status' => $finalStatus]);
                
                $this->createMovementForItems($transfer, $createdItems, 'current_received', 'TRF-IN', 'in', $transfer->to_store_id);
                
                $cost = $this->calculateItemsCost($createdItems, 'current_received');
                if ($cost > 0) {
                    $entry = $this->accountingService->createTransferInEntry($transfer, $cost);
                    if ($entry) {
                        $this->updateJournalEntriesIds($transfer, 'receipts', $entry->id);
                    }
                }

                return $transfer;
            }

            // 4. Return (7 / partial 8)
            if ($newStatus == StDirectTransfer::STATUS_PARTIAL_APPROVED || $newStatus == StDirectTransfer::STATUS_RETURNED) {
            
                $itemsForTotals = empty($itemsData) ? $transfer->items : $createdItems;
                $totalQty       = collect($itemsForTotals)->sum('quantity');
                $totalReceived  = collect($itemsForTotals)->sum('received_quantity');
                $totalReturned  = $totalQty - $totalReceived;

                $isAccountedFor = ($totalReceived + $totalReturned >= $totalQty);

                if ($isAccountedFor) {
                    if ($totalReceived == 0) {
                        $finalStatus = StDirectTransfer::STATUS_RETURNED;
                        $returnStatus = StDirectTransfer::RETURN_STATUS_FULL;
                    } else {
                        $finalStatus = StDirectTransfer::STATUS_DESTINATION_APPROVED;
                        $returnStatus = StDirectTransfer::RETURN_STATUS_PARTIAL;
                    }
                } else {
                    $finalStatus = StDirectTransfer::STATUS_PARTIAL_APPROVED;
                    $returnStatus = StDirectTransfer::RETURN_STATUS_PARTIAL;
                }

                $transfer->update([
                    'status' => $finalStatus,
                    'return_status' => $returnStatus,
                ]);
                
                foreach ($itemsForTotals as $item) {
                    $item->variance_diff = (float)$item->quantity - (float)$item->received_quantity;
                }
                
                try {
                    $this->createMovementForItems($transfer, $itemsForTotals, 'variance_diff', 'TRF-RET-OUT', 'out', $transfer->to_store_id);
                } catch (\Exception $e) {
                }
                
                $this->createMovementForItems($transfer, $itemsForTotals, 'variance_diff', 'TRF-RET-IN', 'in', $transfer->from_store_id);
                
                $cost = $this->calculateItemsCost($itemsForTotals, 'variance_diff');
                if ($cost > 0) {
                    $sourceAccountId = $this->accountingService->getStoreAccount($transfer->from_store_id);
                    $destAccountId   = $this->accountingService->getStoreAccount($transfer->to_store_id);
                    if ($sourceAccountId && $destAccountId) {
                        $entry = $this->accountingService->buildJournalEntry(
                            $transfer,
                            $cost,
                            $sourceAccountId,
                            $destAccountId,
                            "إرجاع تحويل مخزني - " . $transfer->document_number,
                            JournalEntry::ENTRY_TYPE_AUTO
                        );
                        if ($entry) {
                            $this->updateJournalEntriesIds($transfer, 'returns', $entry->id);
                        }
                    }
                }

                return $transfer;
            }

            // 5. Cancellation
            if ($newStatus == StDirectTransfer::STATUS_CANCELLED) {
                $this->handleFullReversal($transfer, $createdItems);
                return $transfer;
            }

            return $transfer;
        });
    }

    // =========================================================================
    // VALIDATE (Destination actions: Draft save / Receipt / Return)
    // =========================================================================

    /**
     * اعتماد استلام التحويل (مسودة وجهة أو اعتماد نهائي)
     */
    public function approveTransfer($id, array $input): StDirectTransfer
    {
        return DB::transaction(function () use ($input, $id) {
            /** @var StDirectTransfer $transfer */
            $transfer  = parent::find($id);
            $newStatus = (int)($input['status'] ?? StDirectTransfer::STATUS_DESTINATION_APPROVED);
            $itemsData = $input['items'] ?? [];

            // 1. تحديث كميات الاستلام فقط
            $items = $this->syncReceiptQuantities($transfer, $itemsData);

            if ($newStatus == StDirectTransfer::STATUS_DESTINATION_DRAFT) {
                return $this->validateDestinationDraft($transfer, $input);
            }

            // 2. معالجة الاعتماد (بدون أي إرجاع)
            return $this->validateDestinationApproval($transfer, $items, $input);
        });
    }

    public function returnTransfer($id, array $input): StDirectTransfer
    {
        return DB::transaction(function () use ($input, $id) {
            /** @var StDirectTransfer $transfer */
            $transfer  = parent::find($id);
            $itemsData = $input['items'] ?? [];

            // 1. تحديث كميات المرتجع فقط
            $items = $this->syncReturnQuantities($transfer, $itemsData);

            // 2. معالجة الإرجاع فقط
            return $this->validateReturnApproval($transfer, $items, $input);
        });
    }

    // -------------------------------------------------------------------------
    // Validate: helpers
    // -------------------------------------------------------------------------

    private function syncReceiptQuantities(StDirectTransfer $transfer, array $itemsData): \Illuminate\Support\Collection
    {
        if (empty($itemsData)) {
            return $transfer->items()->with('product.units')->get()->each(fn($i) => $i->current_received = 0);
        }

        $result = collect();
        foreach ($itemsData as $itemData) {
            $item = $transfer->items()
                ->where('product_id', $itemData['product_id'])
                ->where('have_sizes', $itemData['have_sizes'] ?? 0)
                ->first();

            if (!$item) continue;

            $currentReceived = (float)($itemData['current_received'] ?? 0);
            $newTotalRecv   = (float)($itemData['received_quantity'] ?? ($item->received_quantity + $currentReceived));
            
            $variance = (float)$item->quantity - ($newTotalRecv + (float)$item->returned_quantity);

            $item->update([
                'received_quantity' => $newTotalRecv,
                'variance_quantity' => $variance,
                'notes'             => $itemData['notes'] ?? $item->notes,
            ]);

            $fresh = $item->fresh(['product.units']);
            $fresh->current_received = $currentReceived;
            $result->push($fresh);
        }
        return $result;
    }

    private function syncReturnQuantities(StDirectTransfer $transfer, array $itemsData): \Illuminate\Support\Collection
    {
        if (empty($itemsData)) {
            return $transfer->items()->with('product.units')->get()->each(fn($i) => $i->current_returned = 0);
        }

        $result = collect();
        foreach ($itemsData as $itemData) {
            $item = $transfer->items()
                ->where('product_id', $itemData['product_id'])
                ->where('have_sizes', $itemData['have_sizes'] ?? 0)
                ->first();

            if (!$item) continue;

            $oldReturned   = (float)$item->returned_quantity;
            $newReturned   = (float)($itemData['returned_quantity'] ?? $oldReturned);
            $deltaReturned = max(0, $newReturned - $oldReturned);
            
            $variance = (float)$item->quantity - ((float)$item->received_quantity + $newReturned);

            $item->update([
                'returned_quantity' => $newReturned,
                'variance_quantity' => $variance,
                'notes'             => $itemData['notes'] ?? $item->notes,
            ]);

            $fresh = $item->fresh(['product.units']);
            $fresh->current_returned = $deltaReturned;
            $result->push($fresh);
        }
        return $result;
    }

    private function validateDestinationDraft(StDirectTransfer $transfer, array $input): StDirectTransfer
    {
        $transfer->update([
            'status' => StDirectTransfer::STATUS_DESTINATION_DRAFT,
            'notes'  => $input['notes'] ?? $transfer->notes,
        ]);

        return $transfer;
    }

    private function validateDestinationApproval(
        StDirectTransfer $transfer,
        \Illuminate\Support\Collection $items,
        array $input
    ): StDirectTransfer {
        $totalQty      = $items->sum(fn($i) => (float)$i->quantity);
        $totalReceived = $items->sum(fn($i) => (float)$i->received_quantity);

        $finalStatus = ($totalQty == $totalReceived)
            ? StDirectTransfer::STATUS_DESTINATION_APPROVED
            : StDirectTransfer::STATUS_PARTIAL_APPROVED;

        $transfer->update([
            'status' => $finalStatus,
            'notes'  => $input['notes'] ?? $transfer->notes,
        ]);

        $this->createMovementForItems($transfer, $items, 'current_received', 'TRF-IN', 'in', $transfer->to_store_id);

        $cost = $this->calculateItemsCost($items, 'current_received');
        if ($cost > 0) {
            $entry = $this->accountingService->createTransferInEntry($transfer, $cost);
            if ($entry) {
                $this->updateJournalEntriesIds($transfer, 'receipts', $entry->id);
            }
        }

        return $transfer->fresh();
    }

    private function validateReturnApproval(
        StDirectTransfer $transfer,
        \Illuminate\Support\Collection $items,
        array $input
    ): StDirectTransfer {
        $totalQty      = (float)$items->sum('quantity');
        $totalReceived = (float)$items->sum('received_quantity');
        $totalReturned = (float)$items->sum('returned_quantity');

        $isAccountedFor = ($totalReceived + $totalReturned >= $totalQty);
        
        if ($isAccountedFor) {
            if ($totalReceived == 0) {
                $status = StDirectTransfer::STATUS_RETURNED;
                $returnStatus = StDirectTransfer::RETURN_STATUS_FULL;
            } else {
                $status = StDirectTransfer::STATUS_DESTINATION_APPROVED;
                $returnStatus = StDirectTransfer::RETURN_STATUS_PARTIAL;
            }
        } else {
            $status = StDirectTransfer::STATUS_PARTIAL_APPROVED;
            $returnStatus = StDirectTransfer::RETURN_STATUS_PARTIAL;
        }

        $updateData = [
            'status'            => $status,
            'returned_quantity' => $totalReturned,
            'return_status'     => $returnStatus,
            'notes'             => $input['notes'] ?? $transfer->notes,
        ];

        $transfer->update($updateData);

        $this->createMovementForItems($transfer, $items, 'current_returned', 'TRF-RET-IN', 'in', $transfer->from_store_id);

        $cost = $this->calculateItemsCost($items, 'current_returned');
        if ($cost > 0) {
            $entry = $this->accountingService->createTransferReturnEntry($transfer, $cost);
            if ($entry) {
                $this->updateJournalEntriesIds($transfer, 'returns', $entry->id);
            } else {
                throw new \Exception("فشل إنشاء القيد المحاسبي للإرجاع. يرجى التأكد من إعدادات الحسابات (بضاعة بالطريق).");
            }
        }

        return $transfer->fresh();
    }

    // =========================================================================
    // GENERAL REFATORED JOURNAL & REVERSAL PROCEDURES
    // =========================================================================

    private function generateDirectJournalEntry($transfer)
    {
        $entry = $this->accountingService->createDirectTransferEntry($transfer, $transfer->total_value);
        if ($entry) {
            $this->updateJournalEntriesIds($transfer, 'direct_transfer', $entry->id);
            $transfer->update(['journal_entry_id' => $entry->id]);
        }
    }

    private function generateSourceJournalEntry($transfer)
    {
        $entry = $this->accountingService->createTransferOutEntry($transfer, $transfer->total_value);
        if ($entry) {
            $this->updateJournalEntriesIds($transfer, 'source_approval', $entry->id);
            $transfer->update(['journal_entry_id' => $entry->id]);
        }
    }

    private function handleFullReversal($transfer, $items)
    {
        foreach ($items as $item) {
            $item->in_transit = (float)$item->quantity - (float)$item->received_quantity;
        }

        $this->createMovementForItems($transfer, $items, 'in_transit', 'TRF-REV', 'in', $transfer->from_store_id);

        $reversalCost = $this->calculateItemsCost($items, 'in_transit');
        if ($reversalCost > 0) {
            $sourceAccountId = $this->accountingService->getStoreAccount($transfer->from_store_id);
            $otherAccountId  = $transfer->is_direct
                ? $this->accountingService->getStoreAccount($transfer->to_store_id)
                : $this->accountingService->getMappedAccount('inventory_in_transit');

            if ($sourceAccountId && $otherAccountId) {
                $entry = $this->accountingService->buildJournalEntry(
                    $transfer,
                    $reversalCost,
                    $sourceAccountId,
                    $otherAccountId,
                    "إرجاع تحويل مخزني بالكامل - " . $transfer->document_number,
                    JournalEntry::ENTRY_TYPE_AUTO
                );
                if ($entry) {
                    $this->updateJournalEntriesIds($transfer, 'reversals', $entry->id);
                }
            }
        }
    }

    private function updateJournalEntriesIds($transfer, $key, $entryId)
    {
        $ids = $transfer->journal_entries_ids ?? [];
        if (in_array($key, ['receipts', 'returns', 'reversals'])) {
            if (!isset($ids[$key])) {
                $ids[$key] = [];
            }
            if (!is_array($ids[$key])) {
                $ids[$key] = [$ids[$key]];
            }
            $ids[$key][] = $entryId;
        } else {
            $ids[$key] = $entryId;
        }
        $transfer->update(['journal_entries_ids' => $ids]);
    }

    // =========================================================================
    // REUSABLE HELPER METHODS
    // =========================================================================

    private function createMovementForItems(
        StDirectTransfer $transfer,
        $items,
        string $qtyField,
        string $prefix,
        string $direction,
        int $storeId
    ): void {
        foreach ($items as $item) {
            $qty = (float)($item->$qtyField ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $tempField = '_temp_' . $qtyField;
            $item->setAttribute($tempField, $qty);

            $this->handleStockMovement($transfer, $item, $tempField, [
                'prefix'    => $prefix,
                'type'      => StockMovement::DOC_TYPE_DIRECT_TRANSFER,
                'direction' => $direction,
                'store_id'  => $storeId,
            ]);
        }
    }

    private function calculateItemsCost($items, string $qtyField): float
    {
        return collect($items)->sum(fn($item) => (float)($item->$qtyField ?? 0) * (float)($item->unit_cost ?? 0));
    }
}