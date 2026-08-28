<?php

namespace app\Http\Requests\api;

// use Modules\HR\App\Models\HrAbsentRequests;
 use Illuminate\Foundation\Http\FormRequest;

class ApiJutificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
         'shift_id' => 'required',
        'reason' => 'required|string',
        'type' => 'required|in:1,2,3,4', // 1 = late, 2 = early_leave, 3 = absence
        'request_date' => 'required|date',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB

        ];
    }
}
