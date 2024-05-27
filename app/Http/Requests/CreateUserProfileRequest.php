<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserProfileRequest extends FormRequest
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
            
            'photo' => 'nullable|sometimes|mimes:jpg,png,jpeg,gif,svg',

        ];
    }       

    public function payload(){

        return $this->only([

            'photo',
        ]);

    }

    public function messages()
    {
        // $msg = 'Verification cannot be completed, requirements are not complete.';

        return [


        ];
    }
    
}
