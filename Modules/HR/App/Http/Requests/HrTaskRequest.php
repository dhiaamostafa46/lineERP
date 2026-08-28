<?php

namespace Modules\HR\App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Modules\HR\App\Models\HrTask;

class HrTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return HrTask::rules();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
