<?php

namespace App\Http\Middleware;


use App\Http\Controllers\CodeResponseConstants;
use Closure;
use Illuminate\Support\Facades\Auth;
class CheckAuthorizedAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        if (
            auth()->user()->allow_login === false ||
            auth()->user()->is_blocked === true ||
            auth()->user()->user_type !== 'admin' )
            return api()->forbidden('You are unauthorized to perform this operation', [],$request,CodeResponseConstants::AUTHORIZATION_EXCEPTION_CODE);

        return $next($request);

    }
}
