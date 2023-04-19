<?php

namespace App\ServicesData\Merchant;

use App\Models\Merchant as merchantModel;

class MerchantService
{

    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * Get all with filter merchants
     */
    public static function merchantsFilterAdmin($request)
    {
        $merchants = merchantModel::query();


        if ($request->name)
            $merchants->where('name', 'like', '%' . $request->name . '%');

        if ($request->email)
            $merchants->where('email', 'like', '%' . $request->email . '%');

        if ($request->phone)
            $merchants->where('phone', 'like', '%' . $request->phone . '%');

        if ($request->has('is_active'))
            $merchants->where('allow_login', $request->is_active);

        if ($request->has('white_list_active'))
            $merchants->where('white_list_active', $request->white_list_active);

        if ($request->merchant_key)
            $merchants->where('merchant_key', $request->merchant_key);

        if ($request->white_list)
            $merchants->whereJsonContains('white_list', $request->white_list);

        if ($request->sort)
            $merchants->orderBy('id', $request->sort);


        return $merchants->paginate(isset($request->limit) ? $request->limit : 30)->withQueryString();
    }


    /**
     * @return array
     *
     * reporting section to get total merchants , active merchants and blocked merchants
     */
    public static function reportingAdmin()
    {
        $merchants ['totalMerchant'] = merchantModel::count();
        $merchants ['activeMerchant'] = merchantModel::where('is_active', true)->count();
        $merchants ['blockedMerchant'] = merchantModel::where('is_active', false)->count();

        return $merchants;


    }

    public static function checkExistAndActive($merchantKey)
    {
        return merchantModel::where('merchant_key', $merchantKey)->where('is_active', true)->exists();
    }

    public static function getMerchantDetails($merchantKey)
    {
        return merchantModel::where('merchant_key', $merchantKey)->where('is_active', true)->first();
    }


}
