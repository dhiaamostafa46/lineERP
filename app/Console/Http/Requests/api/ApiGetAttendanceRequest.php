<?php

namespace App\Http\Requests\api;

// use Modules\HR\App\Models\HrAbsentRequests;
 use Illuminate\Foundation\Http\FormRequest;

class ApiGetAttendanceRequest extends FormRequest
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
        'st_date' => 'required|date_format:d-m-Y',
        'end_date' => 'required|date_format:d-m-Y'

        ];
    }
}
