<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class NewPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'password' => 'required',
            'password_confirmation' => 'required'

        ];
    }       

    public function payload(){

        return $this->only([
            'password',
            'password_confirmation'
        ]);

    }

    
}
