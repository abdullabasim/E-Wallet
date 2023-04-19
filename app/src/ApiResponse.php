<?php

namespace App\src;

use App\Http\Controllers\Constants;
use App\ServicesData\Logs\LogsService as logsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Traits\Macroable;
use App\src\Contracts\ApiInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse implements ApiInterface
{
    use Macroable;

    /**
     * Create API response.
     *
     * @param int $status
     * @param string $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function response($status = 200, $message = null, $data = [], $code = 1021, ...$extraData)
    {
        $json = [
            config('api.keys.status') => config('api.stringify') ? strval($status) : $status,
            config('api.keys.message') => $message,
            config('api.keys.data') => $data,
            config('api.keys.code') => $code,
        ];

        if (is_countable($data) && config('api.include_data_count', false) && !empty($data))
            $json = array_merge($json, [config('api.keys.data_count') => config('api.stringify') ? strval(count($data)) : count($data)]);

        if ($extraData)
            foreach ($extraData as $extra)
                $json = array_merge($json, $extra);

        return (config('api.match_status')) ? response()->json($json, $status) : response()->json($json);
    }

    /**
     *
     * @param string $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function ok($message = null, $data = [], $logs = [] ,$code =1021, ...$extraData)
    {
        $message = (is_null($message)) ? Constants::HTTP_OPERATION_SUCCESS : $message;

        return $this->response(Response::HTTP_OK, $message, $data ,$code, ...$extraData);
    }

    /**
     * Create successful (200) API response.
     *
     * @param string $message
     * @param array $data
     * @param int    $code
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function success($message = null, $data = [], $logs = null,$code =1021, ...$extraData)
    {


        if(isset($logs->password))
             $logs->merge(['password' => '']);



        $logsData = $data;

        if(is_array($logsData) && array_key_exists('token',$logsData))
        {
            $logsData['token'] = '';
            $logsData['token_type'] = '';
        }


        logsService::saveLogs(['requestPath' => $logs ? $logs->path() : '',
            'merchant_key' => $logs ? $logs->header('key') : '',
            'request' => $logs ? $logs->all() : '',
            'message' => $message,
            'ip'=>!is_array($logs) ? $logs->ip()  : '',
            'response' => $logsData]);

        return $this->ok($message, $data,$code, ...$extraData);
    }

    /**
     * Create Not found (404) API response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function notFound($message = null , $logs = [] ,$code =1021)
    {

        $message = (is_null($message)) ? Constants::HTTP_NOT_FOUND : $message;

        if(isset($logs->password))
            $logs->merge(['password' => '']);

        logsService::saveLogs(['requestPath' => $logs ? $logs->path() : '',
            'merchant_key' => $logs ? $logs->header('key') : '',
            'request' => $logs ? $logs->all() : '',
            'ip'=>!is_array($logs) ? $logs->ip()  : '',
            'response' => $message]);

        return $this->response(Response::HTTP_NOT_FOUND, $message, [] ,$code );
    }

    /**
     * Create Validation (422) API response.
     *
     * @param string $message
     * @param array $errors
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function validation($message = null, $errors = [], $logs = [] , $code =1021, ...$extraData)
    {


        $message = (is_null($message)) ? Constants::HTTP_INVALID : $message;

        if(isset($logs->password))
            $logs->merge(['password' => '']);


        logsService::saveLogs(['requestPath' => $logs ? $logs->path() : '',
            'merchant_key' => $logs ? $logs->header('key') : '',
            'request' => $logs ? $logs->all() : '',
            'ip'=>!is_array($logs) ? $logs->ip()  : '',
            'response' => $message]);

        return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $errors,$code, ...$extraData);
    }

    /**
     * Create forbidden (403) API response.
     *
     * @param string $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function forbidden($message = null, $data = [], $logs = [],$code=1021, ...$extraData)
    {

        $message = (is_null($message)) ? Constants::HTTP_INVALID : $message;

        if(isset($logs->password))
            $logs->merge(['password' => '']);

        logsService::saveLogs(['requestPath' => $logs ? $logs->path() : '',
            'merchant_key' => $logs ? $logs->header('key') : '',
            'request' => $logs ? $logs->all() : '',
            'message' => $message,
            'ip'=>!is_array($logs) ? $logs->ip()  : '',
            'response' => $data]);

        return $this->response(Response::HTTP_FORBIDDEN, $message, $data,$code, ...$extraData);
    }


    /**
     * Create Server error (500) API response.
     *
     * @param string $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function error($message = null, $data = [], $logs = [] ,$code=1021, ...$extraData)
    {
        $message = (is_null($message)) ? Constants::HTTP_ERROR : $message;

        if(isset($logs->password))
            $logs->merge(['password' => '']);
        logsService::saveLogs(['requestPath' => $logs ? $logs->path() : '',
            'merchant_key' => $logs ? $logs->header('key') : '',
            'request' => $logs ? $logs->all() : '',
            'message' => $message,
            'ip'=>!is_array($logs) ? $logs->ip()  : '',
            'response' => $data]);

        return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $data ,$code, ...$extraData);
    }


}
