<?php

namespace App\ServicesData\Transactions;

use Bavix\Wallet\Models\Transaction as transactionModel;

class TransactionsWalletService
{

    /**
     * @param $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     *
     * Get All Transactions Per Wallet
     */
    public static function getAllTransactionsPerWallet($request)
    {

        $merchantKey = $request->header('key');
        $walletId = $request->wallet_id;

        $transactions = transactionModel::with('payable')->whereHas('payable.merchants', function ($query) use ($merchantKey) {
            $query->where('merchant_key', $merchantKey);
        });

        if ($walletId)
            $transactions->whereHas('payable', function ($query) use ($walletId) {
                $query->where('wallet_user', $walletId);
            });

        if ($request->sort)
            $transactions->orderBy('id', $request->sort);

        //  return $transactions->paginate(isset($request->limit) ? $request->limit : 30)->withQueryString();
        return $transactions->take(100)->get();
    }


}
