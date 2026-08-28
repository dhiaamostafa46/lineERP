<?php

namespace app\Http\Requests\api;

// use Modules\HR\App\Models\HrAbsentRequests;
 use Illuminate\Foundation\Http\FormRequest;

class ApiChangePasswordRequest extends FormRequest
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
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ];
        // Password::min(10)                      // min length
                //    ->mixedCase()                      // require upper & lower
                  //  ->letters()
                   // ->numbers()
                  //  ->symbols()
                   // ->uncompromised(3),    // checks via HaveIBeenPwned (optional)
    }
}
