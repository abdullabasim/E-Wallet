<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Constants;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginAdmin as LoginRequest;
use App\Http\Requests\User\RegisterAdmin as RegisterRequest;
use App\Http\Requests\User\UpdateUserAdmin as UpdateUserAdminRequest;
use App\Http\Requests\User\FilterUserAdmin as filterUserAdminRequest;
use App\Http\Requests\User\UpdateWalletExecutorEligibilityAdmin as updateWalletEligibilityAdminRequest;
use App\Http\Requests\User\GetUserInfoAdmin as getUserInfoAdminRequest;


use App\ServicesData\User\UserService as userService;
use App\Http\Resources\Admin\UserResource;
use App\ServicesData\Merchant\MerchantService as merchantService;
use App\Models\PaymentPermission as paymentPermissionModel;
use App\ServicesData\PaymentPermission\PaymentPermissionService as paymentPermissionService;

use App\Models\User as userModel;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\Hash;
use function api;
use function config;
use function url;

class UserController extends Controller
{

    /**
     * @param RegisterRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Create new Jizdan Admin
     */
    public function register(RegisterRequest $request)
    {

        $user = userModel::create([
            "name" => $request->name,
            "email" => $request->email,
            'email_verified_at' => true,
            "is_blocked" => $request->is_blocked,
            "allow_login" => $request->user_type !== Constants::USER_TYPES['client'] ? $request->allow_login : false, // not client
            "user_type" => $request->user_type,
            "is_password_changed" => false,
            "password" => Hash::make($request->password),
            "phone"=>$request->phone,
            'remember_token' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),
        ]);

        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['userType'] = $user->user_type;
        $data['allowLogin'] = $user->allow_login;
        $data['isBlocked'] = $user->is_blocked;
        if (in_array($user->user_type, [   Constants::USER_TYPES['company'], Constants::USER_TYPES['client'] ])) //company or client
        {

            if ($request->has('merchant_keys')) {
                $merchantKeys = $request->merchant_keys;

                foreach ($merchantKeys as $merchantKey) {
                    $merchant = merchantService::getMerchantDetails($merchantKey);

                    if ($merchant)
                        $user->merchants()->attach($merchant);
                }
            }

            $faker = \Faker\Factory::create();
            $user->wallet_user = $faker->unique()->numerify('#############');
            $user->save();
            $data['wallet_id'] = $user->wallet_user;

        }

        return api()->success('Account created successfully!', $data, $request, CodeResponseConstants::SUCCESS_CODE);


    }

    /**
     * @param LoginRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Login Jizdan Admin
     */
    public function login(LoginRequest $request)
    {


        $client = new Client();
        $headers = [
            //  'Content-Type' => 'application/json'
        ];
        $options = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => config('service.passport.client_id'),
                'client_secret' => config('service.passport.client_secret'),
                "username" => $request->email,
                "password" => $request->password,
                "scope" => "*"

            ]
        ];
        $requestService = new Psr7Request('POST', url(config('service.passport.login_endpoint')), $headers);
        $response = $client->sendAsync($requestService, $options)->wait();


        $user = userService::getUserByEmail($request->email);


        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('JIZDAN')->accessToken;
                $response = ['token_type' => 'Bearer', 'token' => $token];



                return api()->success('Login done successfully!', $response, $request, CodeResponseConstants::SUCCESS_CODE);


            }
        }


        return api()->error('Something wrong in your credentials please try again later !', [], $request, CodeResponseConstants::ERROR_CODE);

    }


    /**
     * @param UpdateUserAdminRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Update all Jizdan Account
     *
     */
    public function update(UpdateUserAdminRequest $request)
    {


        $user = userModel::findOrFail($request->id);

        $oldUserType = $user->user_type;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_blocked = $request->is_blocked;
        $user->allow_login = $request->user_type !== Constants::USER_TYPES['client'] ? $request->allow_login : false;// not client
        $user->user_type = $request->user_type;
        $user->phone = $request->phone;
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->is_password_changed = true;
        }


        if (in_array($oldUserType, [Constants::USER_TYPES['client'], Constants::USER_TYPES['company'] ])) //company or client
        {
            if ($request->has('merchant_keys')) {
                $user->merchants()->detach();
                $merchantKeys = $request->merchant_keys;

                foreach ($merchantKeys as $merchantKey) {
                    $merchant = merchantService::getMerchantDetails($merchantKey);

                    if ($merchant)
                        $user->merchants()->attach($merchant);
                }
            }


        } else
            $user->merchants()->detach();

        $user->save();
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['userType'] = $user->user_type;
        $data['allowLogin'] = $user->allow_login;
        $data['isBlocked'] = $user->is_blocked;


        return api()->success('Account Updated successfully!', $data, $request, CodeResponseConstants::SUCCESS_CODE);

    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * Get all Jizdan Users with filter
     */
    public function usersFilter(filterUserAdminRequest $request)
    {


        $users = userService::usersFilterAdmin($request);

        return UserResource::collection($users);


    }

    /**
     * @param updateWalletEligibilityAdminRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Update
     */
    public function updateWalletExecutorEligibility(updateWalletEligibilityAdminRequest $request)
    {


        $beneficiaryWallets = $request->beneficiary_wallets;

        $executorUser = userService::getWalletOwnerInfo($request->executor_wallet, $request->merchant_key);

        paymentPermissionService::deleteAllBeneficiaryForExecutor($executorUser, $request->merchant_key);


        foreach ($beneficiaryWallets as $beneficiaryWallet) {
            $beneficiaryUser = userService::getWalletOwnerInfo((int)$beneficiaryWallet, $request->merchant_key);


            if ($beneficiaryUser)
                paymentPermissionModel::create([
                    'user_id' => $executorUser->id,
                    'user_has_permission' => $beneficiaryUser->id
                ]);

        }

        return api()->success('Operation done successfully!', [], $request, CodeResponseConstants::SUCCESS_CODE);
    }


    /**
     * @param getUserInfoAdminRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Get user information based on user id
     */
    public function getUserInfo(getUserInfoAdminRequest $request)
    {

        $user = userService::getUserInfo($request->user_id);

        return api()->success('Fetch data done successfully!', $user, $request, CodeResponseConstants::SUCCESS_CODE);


    }


}
