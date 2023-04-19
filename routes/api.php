<?php

/** @var \Laravel\Lumen\Routing\Router $router */



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {

    return '<h3>Welcome to Jizdan!</h3>';
});

//Company Routes
$router->group(['prefix' => 'api/company','namespace' => 'Api\Company'], function () use ($router) {

    $router->post('login', 'UserController@login');



    $router->group([ 'middleware' => ['auth','checkAuthorizedCompanyAndClient']], function () use ($router) {

        $router->post('register', 'UserController@register');

        $router->post('walletBalance', 'WalletController@checkBalance');

        $router->post('depositOperation', 'WalletController@depositOperation');

        $router->post('withdrawOperation', 'WalletController@withdrawOperation');

        $router->post('withdrawOnBehalfOperation', 'WalletController@withdrawOnBehalfOperation');

        $router->post('voucherRedeemOperation', 'VoucherController@voucherRedeemOperation');
        $router->get('vouchersFilter', 'VoucherController@vouchersFilter');


        $router->get('walletTransactions', 'WalletController@walletTransactions');

        $router->post('executorOnWallet', 'WalletController@executorOnWallet');

        $router->post('beneficiaryFromWallet', 'WalletController@beneficiaryFromWallet');

        $router->post('eligiblePaymentOnWalletChecker', 'WalletController@eligiblePaymentOnWalletChecker');

        $router->get('companyInfo', 'UserController@companyInfo');


    });
});


//Admin Routes
$router->group(['prefix' => 'api/admin','namespace' => 'Api\Admin'], function () use ($router) {

    $router->post('login', 'UserController@login');


    $router->group([ 'middleware' => ['auth','checkAuthorizedAdmin']], function () use ($router) {

    $router->post('register', 'UserController@register');
    $router->put('updateUser', 'UserController@update');

    $router->get('usersFilter', 'UserController@usersFilter');
    $router->get('getUserInfo', 'UserController@getUserInfo');

    $router->patch('updateWalletExecutorEligibility', 'UserController@updateWalletExecutorEligibility');

    $router->post('generateVoucher', 'VoucherController@generateVoucher');
    $router->get('vouchersFilter', 'VoucherController@vouchersFilter');



    $router->get('merchantsFilter', 'MerchantController@merchantsFilter');
    $router->post('createMerchant', 'MerchantController@store');
    $router->put('updateMerchant', 'MerchantController@update');


    $router->get('reportingData', 'ReportingController@reportingData');

    });
});
