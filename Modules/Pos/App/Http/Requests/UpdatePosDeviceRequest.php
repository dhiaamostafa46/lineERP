<?php

namespace Modules\Pos\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePosDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'store_id' => 'required|integer|exists:stores,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'default_customer_id' => 'required|integer|exists:inv_customers,id',
            'is_active' => 'nullable|boolean',
            'expense_account_id' => 'nullable|integer',
            'enable_cash_movements' => 'nullable|boolean',
        ];
    }
}
