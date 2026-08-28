<?php

namespace Modules\HR\App\Http\Requests;

class UpdateHrPostRequest extends CreateHrPostRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['image'] = 'nullable|image|max:5120';

        return $rules;
    }
}
