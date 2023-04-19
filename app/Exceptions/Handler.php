<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CodeResponseConstants;
use Throwable;
use App\ServicesData\Logs\LogsService as logsService;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */


    public function render($request, Throwable $exception)
    {





        if(isset($request->password))
            $request->merge(['password' => '']);


        logsService::saveLogs(['requestPath'=>$request->path(),

            'headers'=>$request->header(),
            'request'=>$request->all(),
            'errorCode' => class_basename($exception)]);


        switch(class_basename($exception)){
            case 'TokenMismatchException':
                return api()->error( 'Something wrong in your credentials please try again later !',[],[],CodeResponseConstants::TOKEN_MISMATCH_CODE);

                break;
            case 'ThrottleRequestsException':
                return api()->error( 'You have been rate limited, please try again shortly !',[],[],CodeResponseConstants::THROTTLE_REQUESTS_CODE);
                break;
            case 'MethodNotAllowedHttpException':

                return api()->error( 'Method Not Allowed !',[],[],CodeResponseConstants::METHOD_NOT_ALLOWED_CODE);
                break;
            case 'NotFoundHttpException':
                return api()->notFound( 'We could not locate the data you requested or end point not found !',[],CodeResponseConstants::NOT_FOUND_CODE);
                break;
            case 'MaintenanceModeException':

                return api()->error( 'Jizdan Application is currently down for maintenance, please check back with us soon',[],[],CodeResponseConstants::MAINTENANCE_MODE_CODE);
                break;
            case 'AuthorizationException':

                return api()->forbidden( 'You are unauthorized to perform this operation',[],[],CodeResponseConstants::AUTHORIZATION_EXCEPTION_CODE);
                break;

            case 'ValidationException':

                return api()->validation( null,isset($exception->response->original) ? $exception->response->original : $exception,[],CodeResponseConstants::VALIDATION_CODE);

                break;
        //   default :
        //        return api()->error( 'Something wrong , please try again later !',[],[],CodeResponseConstants::GENERAL_ERROR_CODE);
        }

       return parent::render($request, $exception);

    }
}
