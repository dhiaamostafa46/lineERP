<?php

namespace Modules\Pos\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePosPaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:cash,bank,credit',
            'account_id' => 'required|integer|exists:tree_accounts,id',
            'is_active' => 'nullable|boolean',
        ];
    }
}
