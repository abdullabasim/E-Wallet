<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Voucher\GenerateVoucherAdmin as generateVoucherRequest;
use App\Http\Requests\Voucher\VouchersFilterAdmin as vouchersFilterAdminRequest;
use App\Http\Resources\Admin\VoucherFilterResource;

use App\ServicesData\Voucher\VoucherService as voucherService;

class VoucherController extends Controller
{
    /**
     * @param generateVoucherRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     *
     * Generate Vouchers by admin
     */
    public function generateVoucher(generateVoucherRequest $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $vouchers = voucherService::vouchersGenerator(['total' => $request->total, 'amount' => $request->amount]);


        if ($vouchers)
            return api()->success('Vouchers created successfully!', [], $request, CodeResponseConstants::SUCCESS_CODE);

        return api()->error('Vouchers Not created!', [], $request, CodeResponseConstants::ERROR_CODE);


    }

    public function vouchersFilter(vouchersFilterAdminRequest $request)
    {
        $vouchers = voucherService::vouchersFilterAdmin($request);
        return VoucherFilterResource::collection($vouchers);
    }
}
