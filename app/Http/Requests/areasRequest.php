<?php

namespace App\Http\Requests;

use App\Models\Area;
use Illuminate\Foundation\Http\FormRequest;

class areasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('area');
        if ($id instanceof Area) {
            $id = $id->getKey();
        }

        return Area::rules($id ? (int) $id : null);
    }
}
