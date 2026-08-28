<?php
namespace Modules\Store\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StDirectTransferItem;

class StDirectTransferItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'direct_transfer_id', 'product_id', 'unit_id', 'quantity', 'unit_cost', 'total_cost', 'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StDirectTransferItem::class;
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
