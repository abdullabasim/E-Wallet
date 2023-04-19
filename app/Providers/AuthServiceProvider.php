<?php

namespace App\Providers;

use App\Models\User;
use Dusterio\LumenPassport\LumenPassport;
use Illuminate\Support\ServiceProvider;


// use Nomadnt\LumenPassport\Passport;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * RegisterCompany any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        // register passport routes
        //    LumenPassport::routes();

        //    // change the default token expiration
        //    LumenPassport::tokensExpireIn(Carbon::now()->addDays(15));

        LumenPassport::routes($this->app);

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}
