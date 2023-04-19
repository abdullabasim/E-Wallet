<?php

namespace App\Http\Requests\Merchant;

use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

class MerchantFilterAdmin extends FormRequest
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
            "phone" => ["nullable", "string"],
            "is_active" => ["nullable", "boolean"],
            "merchant_key" => ["nullable", "string"],
            "white_list" => ["nullable", "string"],
            "limit" => ["nullable", "numeric","min:1"],
            "sort" => ["nullable","string",Rule::in(["asc","desc"])],
        ];
    }
}
