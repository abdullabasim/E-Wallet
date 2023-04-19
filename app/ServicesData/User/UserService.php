<?php

namespace App\ServicesData\User;

use App\Http\Controllers\Constants;
use App\Models\User as userModel;

class UserService
{
    /**
     * @param $walletId
     * @param $merchantKey
     * @return mixed
     *
     * get wallet owner information
     */
    public static function getWalletOwnerInfo($walletId, $merchantKey)
    {
        return userModel::where('wallet_user', $walletId)->whereHas('merchants', function ($query) use ($merchantKey) {
            $query->where('merchant_key', $merchantKey);
        })->firstOr(function () {
            return null;
        });
    }

    /**
     * @param $merchantKey
     * @return mixed
     *
     * Check if the wallet related to merchant
     */
    public static function checkWalletMerchantAuthorization($merchantKey, $user)
    {


        $walletMerchant = false;

        if ($merchantKey && $user)
            $walletMerchant = UserModel::where('wallet_user', $user->wallet_user)->whereHas('merchants', function ($query) use ($merchantKey) {
                $query->where('merchant_key', $merchantKey);
            })->exists();

        return $walletMerchant ? true : false;


    }


    /**
     * @param $merchantKey
     * @return mixed
     *
     * get total client related to merchant
     */
    public static function getMerchantClientsTotal($merchantKey)
    {
        return UserModel::whereHas('merchants', function ($query) use ($merchantKey) {
            $query->where('merchant_key', $merchantKey);
        })->where('user_type', Constants::USER_TYPES['client'])->count(); //user type client
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * use to get all users and filter
     */
    public static function usersFilterAdmin($request)
    {
        $users = UserModel::query()->with('usersExecutor');
        if ($request->name)
            $users->where('name', 'like', '%' . $request->name . '%');

        if ($request->email)
            $users->where('email', 'like', '%' . $request->email . '%');

        if ($request->phone)
            $users->where('phone',  $request->phone);

        if ($request->user_type)
            $users->where('user_type', $request->user_type);

        if ($request->has('is_blocked'))
            $users->where('is_blocked', $request->is_blocked);

        if ($request->has('allow_login'))
            $users->where('allow_login', $request->allow_login);

        if ($request->wallet_id)
            $users->where('wallet_user', $request->wallet_id);

        if ($request->merchant_name) {
            $merchantName = $request->merchant_name;
            $users->whereHas('merchants', function ($query) use ($merchantName) {
                $query->where('name', 'like', '%' . $merchantName . '%');
            });
        }
        if ($request->sort)
            $users->orderBy('id', $request->sort);


        return $users->paginate(isset($request->limit) ? $request->limit : 30)->withQueryString();


    }


    /**
     * @return array
     *
     * reporting section to get total users , active users and blocked users
     */
    public static function reportingAdmin()
    {
        $users ['totalUser'] = UserModel::where('user_type', Constants::USER_TYPES['client'])->count(); //user type client
        $users ['activeUser'] = UserModel::where('user_type', Constants::USER_TYPES['client'])->where('is_blocked', false)->count(); //user type client
        $users ['blockedUser'] = UserModel::where('user_type', Constants::USER_TYPES['client'])->where('is_blocked', true)->count(); //user type client

        return $users;


    }


    /**
     * @param $userId
     * @return mixed
     *
     * Get Users Executor  based on Beneficiaries user id
     */
    public static function getUsersExecutor($userId)
    {
        return userModel::whereHas('usersExecutor', function ($query) use ($userId) {
            $query->where('user_has_permission', $userId);
        })->select('id', 'name', 'wallet_user')->get()->toArray();
    }

    /**
     * @param $userId
     * @return mixed
     *
     * Get Users Beneficiaries based on executor user id
     */
    public static function getUsersBeneficiary($userId)
    {
        return userModel::whereHas('usersBeneficiary', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->select('id', 'name', 'wallet_user')->get()->toArray();
    }

    /**
     * @param $userId
     * @return void
     *
     * Get user information based on user id
     */
    public static function getUserInfo($userId)
    {
        return userModel::with(['usersExecutor.userExecutor', 'usersBeneficiary.usersBeneficiary', 'merchants'])->where('id', $userId)->first();
    }


    /**
     * @param $email
     * @return mixed
     *
     * Get user details by email
     */
    public static function getUserByEmail($email)
    {
        return userModel::where('email', $email)->first();
    }

}
