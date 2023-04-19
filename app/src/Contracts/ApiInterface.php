<?php

namespace App\src\Contracts;

use Illuminate\Http\JsonResponse;

interface ApiInterface
{
    /**
     * Create API response.
     *
     * @param int    $status
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function response($status = 200, $message = null, $data = [] ,$code =1021, ...$extraData);

    /**
     * Create successful (200) API response.
     *
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function ok($message = null, $data = [],$code =1021, ...$extraData);

    /**
     * Create Not found (404) API response.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function notFound($message = null , $logs = [] ,$code =1021);

    /**
     * Create Validation (422) API response.
     *
     * @param string $message
     * @param array  $errors
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function validation($message = null, $errors = [] ,$code =1021, ...$extraData);

    /**
     * Create Validation (422) API response.
     *
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function forbidden($message = null, $data = [] ,$code =1021, ...$extraData);

    /**
     * Create Server error (500) API response.
     *
     * @param string $message
     * @param array  $data
     * @param array  $extraData
     *
     * @return JsonResponse
     */
    public function error($message = null, $data = [] ,$code =1021, ...$extraData);
}
