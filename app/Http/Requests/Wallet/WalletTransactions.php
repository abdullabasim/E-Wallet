<?php

namespace App\Http\Requests\Wallet;

use Anik\Form\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ServicesData\User\UserService as userService;

class WalletTransactions extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        $walletOwnerInfo = true;
        if (Request::input('wallet_id'))
            $walletOwnerInfo = userService::getWalletOwnerInfo(Request::input('wallet_id'), Request::header('key'));


        $checkWalletMerchantAuthorization = userService::checkWalletMerchantAuthorization(Request::header('key'), auth()->user());

        if (!$checkWalletMerchantAuthorization ||
            !$walletOwnerInfo)
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
            'wallet_id' => ["nullable", "numeric"],
            "limit" => ["nullable", "numeric", "min:1"],
            "sort" => ["nullable", "string", Rule::in(["asc", "desc"])],
        ];
    }
}
