<?php

namespace Modules\Store\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StSettlementItem;

class StSettlementItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'settlement_id', 'product_id', 'unit_id', 'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StSettlementItem::class;
    }

    /**
     * Delete items matching given conditions
     */
    public function deleteWhere(array $where): void
    {
        $query = StSettlementItem::query();
        foreach ($where as $column => $value) {
            $query->where($column, $value);
        }
        $query->delete();
    }
}
