<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use App\Rules\MerchantExistAndActive as MerchantExistAndActiveRule;
use Illuminate\Validation\Rule;

class UpdateUserAdmin extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {

        return [
            "id" => ["required", "numeric"],
            "name" => ["required", "string", "unique:users,name,".$this->id],
            "email" => ["required", "email", "unique:users,email,".$this->id],
            "password" => ["nullable", "string", "regex:/^.*(?=.{8,})(?=.*[a-zA])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@#$%^&*_]).*$/"],
            "user_type"=>["required" ,"string",Rule::in(Constants::USER_TYPES)],
            "is_blocked" => ["required", "boolean"],
            "allow_login" => ["required", "boolean"],
            "phone" => ["required", "regex:/^((\+|00))|(0)(7[5789])([0-9]{7,8})$/", Rule::unique('users', 'phone')->ignore($this->id)],
            "merchant_keys" => ["required_unless:user_type,admin","array"],
            "merchant_keys.*" => [ new MerchantExistAndActiveRule($this->user_type)],
        ];
    }

    protected function messages() : array
    {
        return [
            'password.regex' => 'The password must contain at least 8 characters with categories, capital characters, small characters, numbers, and special characters.',
            'phone.regex' => 'Phone number format is invalid should be 11 digits ex: 079xxxxxxxx , 078xxxxxxxx , 077xxxxxxxx ,075xxxxxxxx)'

        ];
    }
}
