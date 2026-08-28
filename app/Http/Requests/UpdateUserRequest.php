<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            //$rules = User::$rules;
        
            // استخراج معرف المستخدم من الراوتر
            $userId = $this->route('user');
        
            
            // يجب أن يكون اسم البارامتر 'user' مطابقاً لما هو في الراوتر
        
            $rules['email']    = 'required|email|unique:users,email,' . $userId;
            //$rules['phone']    = 'required|numeric|unique:users,phone,' . $userId;
            $rules['username'] = 'nullable|string|max:255|unique:users,username,' . $userId;
            $rules['password'] = 'nullable|string|min:6';
        
            return $rules;
        }
}
