<?php

namespace App\Http\Requests\Merchant;

use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

class CreateMerchant extends FormRequest
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
            "name" => ["required", "string"],
            "phone" => ["required", "regex:/^((\+|00))|(0)(7[5789])([0-9]{7,8})$/", Rule::unique('merchants', 'phone')->ignore($this->id)],
            "email" => ["nullable", "email"],
            "white_list_active" => ["required", "boolean"],
            "white_list"=>["required","array"],
            "white_list.*"=>["required","ip"],
            "is_active" => ["required", "boolean"],
        ];
    }
}
