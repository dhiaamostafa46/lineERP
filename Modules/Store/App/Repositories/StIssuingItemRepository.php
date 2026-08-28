<?php
namespace Modules\Store\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StIssuingItem;

class StIssuingItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'issuing_id', 'product_id', 'unit_id', 'quantity', 'unit_cost', 'total_cost', 'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StIssuingItem::class;
    }

    public function deleteWhere(array $where)
    {
        $query = $this->model->newQuery();
        foreach ($where as $column => $value) {
            $query->where($column, $value);
        }
        return $query->delete();
    }
}
