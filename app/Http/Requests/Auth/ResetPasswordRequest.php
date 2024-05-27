<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'email' => 'required|email'
            
        ];
    }       

    public function payload(){

        return $this->only([
            'email'
        ]);

    }

    public function messages()
    {
        return [

            'email.required' => 'Please provide an Email address.'
            
        ];
    }



    
}
