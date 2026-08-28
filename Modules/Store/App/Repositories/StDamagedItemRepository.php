<?php

namespace Modules\Store\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StDamagedItem;

class StDamagedItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
      'damaged_id', 'product_id', 'unit_id', 'have_sizes', 'quantity', 'unit_cost', 'total_cost', 'status', 'notes'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StDamagedItem::class;
    }

    public function deleteWhere($id)
    {
        // يقبل array مثل ['damaged_id' => 1] أو scalar مثل 1
        if (is_array($id)) {
            $query = $this->model->newQuery();
            foreach ($id as $column => $value) {
                $query->where($column, $value);
            }
            return $query->delete();
        }
        return $this->model->where('damaged_id', $id)->delete();
    }
}
