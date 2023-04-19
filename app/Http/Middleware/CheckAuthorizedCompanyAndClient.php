<?php

namespace App\Http\Middleware;


use App\Http\Controllers\CodeResponseConstants;
use App\Models\Merchant as merchantModel;
use App\Models\User as UserModel;
use Closure;
use App\ServicesData\User\UserService as userService;
use App\ServicesData\Merchant\MerchantService as merchantService;
class CheckAuthorizedCompanyAndClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {



        $key = $request->header('key');

        $merchant =  merchantService::getMerchantDetails($key);
        $checkWalletMerchantAuthorization =  userService::checkWalletMerchantAuthorization($key ,auth()->user());



        if ( !$merchant ||
            !$checkWalletMerchantAuthorization ||
            ($merchant->white_list_active === true  && !in_array($request->ip(),  collect($merchant->white_list)->toArray())) ||
            auth()->user()->allow_login === false ||
            auth()->user()->is_blocked === true ||
            auth()->user()->user_type !== 'company' )
            return api()->forbidden('You are unauthorized to perform this operation', [],$request,CodeResponseConstants::AUTHORIZATION_EXCEPTION_CODE);



        return $next($request);

    }
}
