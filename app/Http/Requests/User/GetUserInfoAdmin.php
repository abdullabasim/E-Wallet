<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;

class GetUserInfoAdmin extends FormRequest
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
            "user_id" => ["required", "numeric", "exists:users,id"],
        ];
    }
}
