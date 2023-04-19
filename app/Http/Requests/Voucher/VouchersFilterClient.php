<?php

namespace App\Http\Requests\Voucher;

use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

class VouchersFilterClient extends FormRequest
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
            "amount" => ["nullable", "string"],
            "start_at" => ["nullable", "date","date_format:Y-m-d"],
            "expires_at" => ["nullable", "date","date_format:Y-m-d" ,"after_or_equal:start_at"],
            "is_enabled" => ["nullable", "boolean"],
            "is_used" => ["nullable", "boolean"],
            "serial_number" => ["nullable", "string"],
            "used_by" => ["nullable", "string"],
            "limit" => ["nullable", "numeric","min:1"],
            "sort" => ["nullable","string",Rule::in(["asc","desc"])],
        ];
    }
}
