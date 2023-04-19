<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\ServicesData\User\UserService as userService;
use App\ServicesData\Voucher\VoucherService as voucherService;
use App\ServicesData\Merchant\MerchantService as merchantService;
use App\ServicesData\Wallets\WalletService as WalletReportingService;

class ReportingController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * reporting section from users , wallets , vouchers and merchants
     */
    public function reportingData()
    {
        $data ['users'] = userService::reportingAdmin();
        $data ['wallets'] = WalletReportingService::reportingWalletAdmin();
        $data ['vouchers'] = voucherService::reportingAdmin();
        $data ['merchants'] = merchantService::reportingAdmin();

        return api()->success('Fetch data done successfully!', $data, [], CodeResponseConstants::SUCCESS_CODE);

    }

}
