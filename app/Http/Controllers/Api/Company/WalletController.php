<?php

namespace App\Http\Controllers\Api\Company;
use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\CheckBalance as checkBalanceRequest;
use App\Http\Requests\Wallet\DepositOperation as depositOperationRequest;
use App\Http\Requests\Wallet\WithdrawOnBehalfOperation as withdrawOnBehalfOperationRequest;
use App\Http\Requests\Wallet\WithdrawOperation as withdrawOperationRequest;
use App\Http\Requests\Wallet\WalletTransactions as walletTransactionsRequest;
use App\Http\Requests\Wallet\ExecutorOnWallet as executorOnWalletRequest;
use App\Http\Requests\Wallet\BeneficiaryFromWallet as beneficiaryFromWalletRequest;
use App\Http\Requests\Wallet\EligiblePaymentOnWallet as eligiblePaymentOnWalletRequest;

use App\ServicesData\PaymentPermission\PaymentPermissionService as paymentPermissionService;
use App\ServicesData\User\UserService as userService;
use function api;
use function auth;

use App\ServicesData\Transactions\TransactionsWalletService as TransactionsWalletService;
use App\Http\Resources\Company\WalletTransactionsResource ;
class WalletController extends Controller
{


    /**
     * @param checkBalanceRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     *
     * Get User Balance
     */
   public function checkBalance(checkBalanceRequest $request)
   {





           $walletOwnerInfo = userService::getWalletOwnerInfo($request->wallet_id,$request->header('key'));

           if( isset($walletOwnerInfo->balance))
               return api()->success("Fetch data done successfully",['balance'=>$walletOwnerInfo->balance],$request);

               return api()->notFound('You done have a wallet !',$request);





   }

    /**
     * @param depositOperationRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     *
     *  perform deposit operation
     */
   public function depositOperation(depositOperationRequest $request)
   {


           $walletOwnerInfo = userService::getWalletOwnerInfo($request->wallet_id,$request->header('key'));




           if( isset($walletOwnerInfo->balance) )
           {
               $walletOwnerInfo->deposit($request->amount);
               return api()->success("Deposit Operation done successfully",['balance'=>$walletOwnerInfo->balance],$request,CodeResponseConstants::SUCCESS_CODE);

           }
               return api()->error('Your request not correct , please try again later !',[],$request,CodeResponseConstants::ERROR_CODE);



   }

    /**
     * @param withdrawOperationRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     *
     * perform withdraw operation
     */
    public function withdrawOperation(withdrawOperationRequest $request)
    {

            $walletOwnerInfo = userService::getWalletOwnerInfo($request->wallet_id,$request->header('key'));

            if( isset($walletOwnerInfo->balance))

            {
                if($walletOwnerInfo->balance  - $request->amount >= 0)
                {
                    $walletOwnerInfo->withdraw($request->amount);
                    auth()->user()->deposit($request->amount);

                    return api()->success("Withdraw Operation done successfully",['balance'=>$walletOwnerInfo->balance],$request,CodeResponseConstants::SUCCESS_CODE);

                }

                return api()->error('Insufficient Funds !',[],$request,CodeResponseConstants::ERROR_CODE);
            }
                return api()->error('Your request not correct , please try again later !',[],$request,CodeResponseConstants::ERROR_CODE);



    }

    /**
     * @param withdrawOnBehalfOperationRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     *
     * perform withdraw operation on behalf
     */
    public function withdrawOnBehalfOperation(withdrawOnBehalfOperationRequest $request)
    {




            $walletExecutorInfo = userService::getWalletOwnerInfo($request->executor_wallet_id,$request->header('key'));

            $walletBeneficiaryInfo = userService::getWalletOwnerInfo($request->beneficiary_wallet_id,$request->header('key'));



            if( isset($walletExecutorInfo->balance)   )
            {

                if($walletExecutorInfo->balance  - $request->amount >= 0)
                {
                    $walletExecutorInfo->withdraw($request->amount);

                    return api()->success("Withdraw Operation done successfully",['balance'=>$walletExecutorInfo->balance],$request,CodeResponseConstants::SUCCESS_CODE);


                }


                return api()->error('Insufficient Funds !',[],$request,CodeResponseConstants::ERROR_CODE);
            }

                return api()->error('Your request not correct , please try again later !',[],$request,CodeResponseConstants::ERROR_CODE);



    }

    /**
     * @param walletTransactionsRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * get wallet Transactions per merchant and wallet id
     */
    public function walletTransactions(walletTransactionsRequest $request)
    {
        $transactions = TransactionsWalletService::getAllTransactionsPerWallet($request);

        return  WalletTransactionsResource::collection($transactions);

    }

    /**
     * @param executorOnWalletRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Get all wallet which is eligible to perform operation on wallet entered
     */
    public function executorOnWallet(executorOnWalletRequest $request)
    {


        $wallets = paymentPermissionService::getExecutorOnWallet($request->beneficiary_wallet_id,$request->header('key'));
        return api()->success("Fetch wallets operation done successfully",$wallets,$request,CodeResponseConstants::SUCCESS_CODE);

    }

    /**
     * @param beneficiaryFromWalletRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Get all wallet which is allowed to perform operation for wallet entered
     */
    public function beneficiaryFromWallet(beneficiaryFromWalletRequest $request)
    {
        $wallets = paymentPermissionService::getBeneficiaryFromWallet($request->executor_wallet_id,$request->header('key'));

        return api()->success("Fetch wallets operation done successfully",$wallets,$request,CodeResponseConstants::SUCCESS_CODE);

    }

    /**
     * @param eligiblePaymentOnWalletRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * check if the executor wallet if eligible to perform operation on beneficiary wallet
     */
    public function eligiblePaymentOnWalletChecker(eligiblePaymentOnWalletRequest $request)
    {


        $result = paymentPermissionService::eligiblePayment($request->executor_wallet_id,$request->beneficiary_wallet_id,$request->header('key'));


        return api()->success("Fetch wallets operation done successfully",['result'=>$result],$request,CodeResponseConstants::SUCCESS_CODE);
    }



}
