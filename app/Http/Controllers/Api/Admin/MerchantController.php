<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\CodeResponseConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\CreateMerchant as createMerchantRequest;
use App\Http\Requests\Merchant\UpdateMerchant as updateMerchantRequest;
use App\Http\Requests\Merchant\MerchantFilterAdmin as merchantFilterAdminRequest;
use App\Http\Resources\Admin\MerchantResource;
use App\Models\Merchant as merchantModel;
use App\ServicesData\Merchant\MerchantService as merchantService;
use function api;
use function collect;

class MerchantController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * Fetch all merchants by admin with filter
     */
    public function merchantsFilter(merchantFilterAdminRequest $request)
    {


        $merchants = merchantService::merchantsFilterAdmin($request);

        return MerchantResource::collection($merchants);


    }

    /**
     * @param createMerchantRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Create new merchant by admin
     */
    public function store(createMerchantRequest $request)
    {


        $faker = \Faker\Factory::create();
        $merchant = merchantModel::create([
            'name' => $request->name,
            'phone' => $request->phone,
            "merchant_key" => $faker->unique()->numerify('###########'),
            'email' => $request->email,
            'white_list' => collect($request->white_list),
            "white_list_active" => $request->white_list_active,
            "is_active" => $request->is_active,
        ]);


        return api()->success("Merchant Create successfully", ['merchant' => $merchant], $request, CodeResponseConstants::SUCCESS_CODE);


    }

    /**
     * @param updateMerchantRequest $request
     * @return \App\src\APIResponse|\Illuminate\Http\JsonResponse
     * Update merchant data by admin
     */
    public function update(updateMerchantRequest $request)
    {


        $merchant = merchantModel::findOrFail($request->id);

        $merchant->name = $request->name;
        $merchant->phone = $request->phone;
        $merchant->email = $request->email;
        $merchant->white_list = collect($request->white_list);
        $merchant->white_list_active = $request->white_list_active;
        $merchant->is_active = $request->is_active;
        $merchant->save();

        return api()->success("Merchant Updated successfully", ['merchant' => $merchant], $request, CodeResponseConstants::SUCCESS_CODE);

    }


}
