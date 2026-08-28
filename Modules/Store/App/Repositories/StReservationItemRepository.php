<?php

namespace Modules\Store\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Store\App\Models\StReservationItem;

class StReservationItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'reservation_id',
        'product_id',
        'unit_id',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StReservationItem::class;
    }
}
