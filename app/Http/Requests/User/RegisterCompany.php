<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use App\Rules\MerchantWalletRelationChecker as MerchantWalletCheckerRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\MerchantWalletRelationChecker as MerchantWalletRelationCheckerRule;
class RegisterCompany extends FormRequest
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
            "name" => ["required", "string", "min:4"],
            "phone" => ["required", "regex:/^((\+|00))|(0)(7[5789])([0-9]{7,8})$/", Rule::unique('users', 'phone')->ignore($this->id)],
            "eligible_payment"=>["nullable","array"],
            "eligible_payment.*"=>["numeric","exists:users,wallet_user",new MerchantWalletCheckerRule(Request::header('key'))],

        ];
    }

    protected function messages() : array
    {
        return [
            'phone.regex' => 'Phone number format is invalid should be 11 digits ex: 079xxxxxxxx , 078xxxxxxxx , 077xxxxxxxx ,075xxxxxxxx)'
        ];
    }
}
