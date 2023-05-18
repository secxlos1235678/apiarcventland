<?php

namespace App\Http\Requests\API\v1\User;

use App\Http\Requests\API\v1\User\BaseRequest as FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
	  public function authorize()
    {
        return true;
    }
    public function rules()
    {		
		return [
			'name'	=> 'required',
            'email' => 'required',
            'password' => 'required',
			'username' => 'required'
        ];
    }
}
