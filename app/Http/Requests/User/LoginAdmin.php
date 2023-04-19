<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use Illuminate\Http\Request;
use App\ServicesData\User\UserService as userService;

class LoginAdmin extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        $email = Request::input('email');
        $user = userService::getUserByEmail($email);

        if (!$user || $user->allow_login !== true ||
            $user->is_blocked == true ||
            $user->user_type !== Constants::USER_TYPES['admin']) //admin
            return false;

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
            "email" => ["required", "email"],
            "password" => ["required", "string", "min:6"],
        ];
    }
}
