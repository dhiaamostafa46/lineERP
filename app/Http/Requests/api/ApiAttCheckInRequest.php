<?php

namespace app\Http\Requests\api;

// use Modules\HR\App\Models\HrAbsentRequests;
 use Illuminate\Foundation\Http\FormRequest;

class ApiAttCheckInRequest extends FormRequest
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
            "location_id"=> 'required|integer',
            "latitude"=> 'required|string',
            "longitude"=> 'required|string'


        ];
    }
}
