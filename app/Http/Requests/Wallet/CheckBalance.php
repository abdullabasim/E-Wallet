<?php

namespace App\Http\Requests\Wallet;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use Illuminate\Http\Request;
use App\ServicesData\User\UserService as userService;

class CheckBalance extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {

        $walletOwnerInfo = userService::getWalletOwnerInfo(Request::input('wallet_id'), Request::header('key'));
        $checkWalletMerchantAuthorization = userService::checkWalletMerchantAuthorization(Request::header('key'), auth()->user());

        if (!$walletOwnerInfo ||
            !$checkWalletMerchantAuthorization ||
            $walletOwnerInfo->user_type === Constants::USER_TYPES['admin'] || //user type should not admin
            is_null($walletOwnerInfo->wallet_user))
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
            'wallet_id' => ["required", "numeric"]
        ];
    }
}
