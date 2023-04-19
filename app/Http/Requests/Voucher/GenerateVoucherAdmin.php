<?php

namespace App\Http\Requests\Voucher;

use Anik\Form\FormRequest;

class GenerateVoucherAdmin extends FormRequest
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
            'total'=>["required", "numeric","min:1"],
            "amount" => ["required", "numeric" ,"min:1"],

        ];
    }
}
