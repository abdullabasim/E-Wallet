<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Constants;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginCompany as LoginRequest;
use App\Http\Requests\User\RegisterCompany as RegisterRequest;
use App\Http\Requests\User\CompanyInfo as CompanyInfoRequest;

use App\ServicesData\User\UserService as userService;
use App\ServicesData\Merchant\MerchantService as merchantService;
use App\Models\PaymentPermission as paymentPermissionModel;
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
     * Create new Jizdan Company
     */
    public function register(RegisterRequest $request)
    {




            $faker = \Faker\Factory::create();

            $merchant = merchantService::getMerchantDetails($request->header('key'));

            $user =   userModel::create([
                "name" => $request->name,
                "wallet_user" => $faker->unique()->numerify('#############'),
                "email" => $faker->unique()->companyEmail(),
                "phone"=>$request->phone,
                'email_verified_at' => true,
                "is_blocked" => false,
                "allow_login"=>false,
                "user_type" => Constants::USER_TYPES['client'],
                "is_password_changed" => false,
                "password" => Hash::make($faker->password()),
                'remember_token' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),
            ]);


            $user->merchants()->attach($merchant);

            if($request->has('eligible_payment'))
            {
                $eligiblePayments = $request->eligible_payment;

                foreach ($eligiblePayments as $eligiblePaymentInfo)
                {
                    $executorUser = userService::getWalletOwnerInfo((int)$eligiblePaymentInfo , $merchant->merchant_key);


                    if($executorUser && $executorUser->id !== $user->id)
                        paymentPermissionModel::create([
                            'user_id'=>$executorUser->id,
                            'user_has_permission'=>$user->id
                        ]);

                }
            }



            $data['name'] =  $user->name;
            $data['wallet_id'] = $user->wallet_user;


        return api()->success('Account created successfully!',$data,$request,CodeResponseConstants::SUCCESS_CODE);



    }

    /**
     * @param LoginRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Login Jizdan Company
     */
    public function login(LoginRequest $request)
    {




            $client = new Client();
         //   $headers = [ ];


            $options = [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('service.passport.client_id'),
                    'client_secret' => config('service.passport.client_secret'),
                    "username" => $request->email,
                    "password" => $request->password,
                    "scope"=> "*"

                ]
            ];
            $requestService = new Psr7Request('POST', url(config('service.passport.login_endpoint'))   );
            $response =  $client->sendAsync($requestService, $options)->wait();


            $user = userService::getUserByEmail($request->email);


            if ($user) {


                if (Hash::check($request->password, $user->password)  )
                {

                    $token = $user->createToken('JIZDAN')->accessToken;
                    $response = ['token_type'=>'Bearer','token' => $token ];

                    return api()->success('Login done successfully!',$response,$request,CodeResponseConstants::SUCCESS_CODE);


                }
            }

        return api()->error('Something wrong in your credentials please try again later !',[],[],CodeResponseConstants::ERROR_CODE);


    }

    public function companyInfo(CompanyInfoRequest $request)
    {
       $data['name'] = auth()->user()->name;
       $data['phone'] = auth()->user()->phone;
       $data['wallet_id'] = auth()->user()->wallet_user;
       $data['balance'] = auth()->user()->balance;
       $data['merchant_key'] = $request->header('key');
       $totalClients = userService::getMerchantClientsTotal($request->header('key'));
       $data['total_client'] = $totalClients;
       $data['created_at'] = auth()->user()->created_at;

        return api()->success('Fetch date done successfully!',$data,$request,CodeResponseConstants::SUCCESS_CODE);
    }





}
