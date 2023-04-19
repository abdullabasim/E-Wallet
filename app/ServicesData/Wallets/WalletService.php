<?php

namespace App\ServicesData\Wallets;


use Bavix\Wallet\Models\Wallet as walletModel;
use Bavix\Wallet\Models\Transaction as transactionModel;

class WalletService
{

    /**
     * @return array
     *
     * reporting section to get total Amount in wallets and the used amount from wallets
     */
    public static function reportingWalletAdmin()
    {
        $wallet ['totalAmount'] = walletModel::sum('balance');
        $wallet ['usedAmount'] = abs(transactionModel::where('type', 'withdraw')->sum('amount'));

        return $wallet;


    }


}
