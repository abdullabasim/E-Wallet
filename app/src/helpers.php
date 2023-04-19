<?php

use App\ServicesData\Logs\LogsService as logsService;
use Illuminate\Http\JsonResponse;
use App\src\APIResponse;
use App\src\Contracts\ApiInterface;

if (!function_exists('api')) {

    /**
     * Create a new APIResponse instance.
     *
     * @param int    $status
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $extraData
     *
     * @return APIResponse|JsonResponse
     */
    function api($status = 200, $message = '', $data = [],$logs = [] ,$code = 1021,...$extraData)
    {


        if (func_num_args() === 0)   return app(ApiInterface::class);

        return app(ApiInterface::class)->response($status, $message, $data,$code, ...$extraData);
    }
}

if (!function_exists('ok')) {

    /**
     * Return success response.
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    function ok($message = '', $data = [], $logs = [] ,$code=1021,...$extraData)
    {
        return api()->ok($message, $data,$code, ...$extraData);
    }
}

if (!function_exists('success')) {

    /**
     * Return success response.
     *
     * @param string $message
     * @param array  $data
     * @param int    $code
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    function success($message = '', $data = [],$logs = [] ,$code=1021, ...$extraData)
    {

        return api()->success($message, $data,$code, ...$extraData);
    }
}
