<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDetailsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $id = '';
        if(isset($this->id))
            $id = decrypt($this->id);

        return [
            
            'photo' => 'nullable|sometimes|mimes:jpg,png,jpeg,gif,svg',
            'user.fullname' => 'required',
            'user.email' => 'required|email|unique:sumb_users,email,'. $id,
            // 'user.password' => 'required',
            // 'user.password' => 'required_if|exists:sumb_users,password|same:user.password_confirmation', 
            
            
        ];

    }       

    public function payload(){

        return $this->only([

            'photo',
            'user.fullname',
            'user.email',
            'user.password',
            'user.password_confirmation',
            'user.email_verified_at',
            // 'user_details.city',
            // 'user_details.suburb',
            // 'user_details.state',
            // 'user_details.zip',
            // 'user_details.mobile_number',
            // 'user_details.country_code'
        ]);

    }

    public function messages()
    {
        

        return [

            
            
            
        ];

    }
    
}
