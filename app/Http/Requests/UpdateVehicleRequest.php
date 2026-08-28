<?php

namespace App\Http\Requests;

use App\Models\Vehicles\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
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
        $rules = Vehicle::$rules;
        $rules['license_number'] = ['nullable','string','max:50',Rule::unique('vc_vehicles', 'license_number')->ignore($this->vehicle),];
        $rules['license_expiry_date'] ='nullable|date';
        $rules['license_image'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        $rules['license_reg_type'] = ['nullable','string','max:50'];
       
        
        return $rules;
    }
}
