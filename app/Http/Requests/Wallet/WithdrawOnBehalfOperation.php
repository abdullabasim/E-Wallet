<?php

namespace App\Http\Requests\Wallet;

use Anik\Form\FormRequest;

use App\ServicesData\PaymentPermission\PaymentPermissionService as paymentPermissionService;

use App\ServicesData\User\UserService as userService;
use Illuminate\Http\Request;
use App\Http\Controllers\Constants;

class WithdrawOnBehalfOperation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        $walletExecutorInfo = userService::getWalletOwnerInfo(Request::input('executor_wallet_id'), Request::header('key'));
        $walletBeneficiaryInfo = userService::getWalletOwnerInfo(Request::input('beneficiary_wallet_id'), Request::header('key'));

        $checkWalletMerchantAuthorizationExecutorInfo = userService::checkWalletMerchantAuthorization(Request::header('key'), $walletExecutorInfo);
        $checkWalletMerchantAuthorizationBeneficiaryInfo = userService::checkWalletMerchantAuthorization(Request::header('key'), $walletBeneficiaryInfo);
        $eligiblePayment = paymentPermissionService::eligiblePayment(Request::input('executor_wallet_id'), Request::input('beneficiary_wallet_id'), Request::header('key'));


        if (!$checkWalletMerchantAuthorizationExecutorInfo ||
            !$checkWalletMerchantAuthorizationBeneficiaryInfo ||
            $walletExecutorInfo->id === $walletBeneficiaryInfo->id ||
            $walletExecutorInfo->user_type !== Constants::USER_TYPES['client'] || //not client
            $walletBeneficiaryInfo->user_type !== Constants::USER_TYPES['client'] || //not client
            is_null($walletExecutorInfo->wallet_user) || // does not have wallet
            is_null($walletBeneficiaryInfo->wallet_user) || // does not have wallet
            !$eligiblePayment)

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
            "amount" => ["required", "numeric", "min:1"],
            'executor_wallet_id' => ["required", "numeric"],
            'beneficiary_wallet_id' => ["required", "numeric"],
        ];
    }
}
