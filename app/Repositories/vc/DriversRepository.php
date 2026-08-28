<?php

namespace App\Repositories\vc;

use App\Models\Branch;
use App\Models\Vehicles\Driver;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DriversRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'status',
        'ref_no',
        'name',
        'mobile',
        'email',
        'iqama',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Driver::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): Builder
    {
        $refNo = $search['ref_no'] ?? null;
        unset($search['ref_no']);

        $query = parent::allQuery($search, $skip, $limit);

        if ($refNo) {
            $query->whereHas('companyReferences', function (Builder $referenceQuery) use ($refNo): void {
                $referenceQuery->where('ref_no', 'LIKE', '%'.$refNo.'%');
            });
        }

        return $query;
    }

    public function branches(): array
    {
        return Branch::select('id')
            ->with('translations:branch_id,locale,name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}
