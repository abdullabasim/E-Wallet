<?php

namespace App\Http\Requests\Voucher;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use App\Models\User as UserModel;
use Illuminate\Http\Request;
use App\ServicesData\User\UserService as userService;
class VoucherRedeem extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {

        $walletOwnerInfo = userService::getWalletOwnerInfo(Request::input('wallet_id'),Request::header('key'));
        $checkWalletMerchantAuthorization =  userService::checkWalletMerchantAuthorization(Request::header('key') ,auth()->user());

        if(!$walletOwnerInfo ||
            !$checkWalletMerchantAuthorization ||
            $walletOwnerInfo->user_type === Constants::USER_TYPES['admin'] ||
            is_null($walletOwnerInfo->wallet_user)) //user type should not admin
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
            "pin" => ["required", "digits:15"],
            'wallet_id'=>["required", "numeric"],
        ];
    }
}
