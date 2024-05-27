<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $id = '';
        if(isset($this->userId))
            $id = decrypt($this->userId);

        return [
            
            'email' => 'required|email|unique:sumb_users,email,'. $id,
            'fullname' => 'required',
            // 'password' => 'min:6|max:60|required_with:password_confirmation',
            'accountype' => 'required'

        ];
    }       

    public function payload(){

        return $this->only([

            'email',
            'fullname',
            // 'password',
            'accountype',
            'email_verified_at',
            // 'profilepic'
            // 'password_confirmation'

        ]);

    }

    public function messages()
    {
        // $msg = 'Verification cannot be completed, requirements are not complete.';

        return [

            'email.exist' => 'The email you registered is already in our system, Please try to login.',
            'email.required' => 'Email is required', 
            'fullname.required' => 'Full name is required',
            // 'password.min' => "Password must be atleast 6 characters",
            // 'password.max' => "Password must not exceed 60 characters",
            
        ];
    }
    
}
