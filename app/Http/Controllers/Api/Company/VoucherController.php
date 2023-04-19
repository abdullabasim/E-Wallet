<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\VouchersFilterClient as vouchersFilterClientRequest;
use App\Http\Resources\Company\voucherFilterResource;
use App\ServicesData\User\UserService as userService;
use Illuminate\Http\Request;
use App\Http\Requests\Voucher\VoucherRedeem as voucherRedeemRequest;
use App\ServicesData\Voucher\VoucherService as voucherService;
class VoucherController extends Controller
{
    /**
     * @param voucherRedeemRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Voucher redeem by client and save logs
     */
    public function voucherRedeemOperation(voucherRedeemRequest $request)
    {




            $walletOwnerInfo = userService::getWalletOwnerInfo($request->wallet_id,$request->header('key'));

            $voucherRedeemStatus = voucherService::voucherRedeem($request->pin,$walletOwnerInfo);


            if($voucherRedeemStatus === 'done')
                return api()->success("Voucher Redeem done successfully",['balance'=>$walletOwnerInfo->balance],$request,CodeResponseConstants::SUCCESS_CODE);
            elseif($voucherRedeemStatus === 'used')
                return api()->error('Voucher is used !',[],[],CodeResponseConstants::VOUCHER_USED_CODE);
            else
              return api()->validation('Incorrect Input , please try again later !',[],$request,CodeResponseConstants::VALIDATION_CODE);



    }



    public function vouchersFilter(vouchersFilterClientRequest $request)
    {
        $vouchers = voucherService::vouchersFilterClient($request);
        return voucherFilterResource::collection($vouchers);
    }
}
