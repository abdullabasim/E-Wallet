<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use Illuminate\Validation\Rule;

class FilterUserAdmin extends FormRequest
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
            "name" => ["nullable", "string"],
            "email" => ["nullable", "string"],
            "phone" => ["nullable",  "regex:/^((\+|00))|(0)(7[5789])([0-9]{7,8})$/",],
            "user_type"=>["nullable" ,"string",Rule::in(Constants::USER_TYPES)],
            "is_blocked" => ["nullable", "boolean"],
            "allow_login" => ["nullable", "boolean"],
            "wallet_id" => ["nullable", "numeric"],
            "merchant_name" => ["nullable", "string"],
            "limit" => ["nullable", "numeric","min:1"],
            "sort" => ["nullable","string",Rule::in(["asc","desc"])],
        ];
    }

    protected function messages() : array
    {
        return [
            'phone.regex' => 'Phone number format is invalid should be 11 digits ex: 079xxxxxxxx , 078xxxxxxxx , 077xxxxxxxx ,075xxxxxxxx)'
        ];
    }
}
