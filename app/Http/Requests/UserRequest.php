<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        switch($this->method())
        {
            case 'POST': {
                return [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8',
                    'phone' => 'required|string',
                    'mobile_phone' => 'required|string',
                ];
            }

            case 'PATCH' : {
                return [
                    'name' => 'required|string|max:255',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('users','email')->ignore($this->route()->user)
                    ],
                    'password' => 'string|min:8',
                    'profile.phone' => 'required|string',
                    'profile.mobile_phone' => 'required|string',
                ];
            }
        }
    }
}
