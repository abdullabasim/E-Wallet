<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;
use App\Rules\MerchantWalletRelationChecker as MerchantWalletCheckerRule;
class UpdateWalletExecutorEligibilityAdmin extends FormRequest
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
            'executor_wallet'=>["required","exists:users,wallet_user",new MerchantWalletCheckerRule($this->merchant_key)],
            'beneficiary_wallets'=>["required", "array"],
            'beneficiary_wallets.*'=>["required","numeric","exists:users,wallet_user",new MerchantWalletCheckerRule($this->merchant_key)],
            'merchant_key' => ["required","exists:merchants,merchant_key"],
        ];
    }
}
