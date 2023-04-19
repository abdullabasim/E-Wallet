<?php

namespace App\ServicesData\PaymentPermission;

use App\Models\User as userModel;
use App\Models\PaymentPermission as paymentPermissionModel;
use App\ServicesData\User\UserService as userService;

class PaymentPermissionService
{

    public static function getExecutorOnWallet($beneficiaryWallet, $merchantKey)
    {
        $getWalletOwnerInfo = userService::getWalletOwnerInfo($beneficiaryWallet, $merchantKey);


        $beneficiaryUserId = $getWalletOwnerInfo->id;
        return userModel::whereNotNull('wallet_user')->whereHas('usersExecutor', function ($query) use ($beneficiaryUserId) {
            $query->where('user_has_permission', $beneficiaryUserId);
        })->whereHas('merchants', function ($query) use ($merchantKey) {
            $query->where('merchant_key', $merchantKey);
        })->pluck('wallet_user')->toArray();

    }

    public static function getBeneficiaryFromWallet($executorWallet, $merchantKey)
    {
        $getWalletOwnerInfo = userService::getWalletOwnerInfo($executorWallet, $merchantKey);

        $executorUserId = $getWalletOwnerInfo->id;
        return userModel::whereNotNull('wallet_user')->whereHas('usersBeneficiary', function ($query) use ($executorUserId) {
            $query->where('user_id', $executorUserId);
        })->whereHas('merchants', function ($query) use ($merchantKey) {
            $query->where('merchant_key', $merchantKey);
        })->pluck('wallet_user')->toArray();


    }

    public static function eligiblePayment($executorWalletUser, $beneficiaryWalletUser, $merchantKey)
    {

        return paymentPermissionModel::whereHas('userExecutor', function ($query) use ($executorWalletUser, $merchantKey) {
            $query->where('wallet_user', $executorWalletUser)->whereHas('merchants', function ($query) use ($merchantKey) {
                $query->where('merchant_key', $merchantKey);
            });
        })->whereHas('userBeneficiary', function ($query) use ($beneficiaryWalletUser, $merchantKey) {
            $query->where('wallet_user', $beneficiaryWalletUser)->whereHas('merchants', function ($query) use ($merchantKey) {
                $query->where('merchant_key', $merchantKey);
            });
        })->exists();
    }

    public static function deleteAllBeneficiaryForExecutor($executorUser, $merchantKey)
    {


        return paymentPermissionModel::where('user_id', $executorUser->id)->whereHas('userExecutor.merchants', function ($query) use ($merchantKey) {
            $query->where('merchant_key', $merchantKey);
        })->delete();


    }


}
