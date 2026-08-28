<?php

namespace Modules\Pos\App\Repositories;

use Modules\Pos\App\Models\PosPaymentMethod;
use App\Repositories\BaseRepository;

class PosPaymentMethodRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'type', 'account_id', 'is_active'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        
        $query->with('account');

        return $query->orderBy('id', 'desc');
    }

    public function create(array $input, bool $withLog = true): \Illuminate\Database\Eloquent\Model
    {
        $input['is_active'] = !empty($input['is_active']) ? 1 : 0;
        return parent::create($input, $withLog);
    }

    public function update(array $input, int $id, bool $withLog = true)
    {
        $input['is_active'] = !empty($input['is_active']) ? 1 : 0;
        return parent::update($input, $id, $withLog);
    }

    public function model(): string
    {
        return PosPaymentMethod::class;
    }

    public function header(): array
    {
        return [
            __('pos::models/payment_methods.fields.id'),
            __('pos::models/payment_methods.fields.name'),
            __('pos::models/payment_methods.fields.type'),
            __('pos::models/payment_methods.fields.account_id'),
            __('pos::models/payment_methods.fields.is_active'),
            __('pos::models/payment_methods.fields.created_at'),
        ];
    }

    public function dataExel(): array
    {
        return PosPaymentMethod::with('account')
            ->get()
            ->map(function ($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'type' => $method->type_text,
                    'account_id' => $method->account ? $method->account->name : '',
                    'is_active' => $method->is_active_text,
                    'created_at' => $method->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('pos::models/payment_methods.singular');
    }
}
