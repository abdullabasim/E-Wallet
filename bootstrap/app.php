<?php



require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Baghdad'));
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);


/*
|--------------------------------------------------------------------------
| RegisterCompany Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Console\Kernel::class
);


/*
|--------------------------------------------------------------------------
| RegisterCompany Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/



/*
|--------------------------------------------------------------------------
| RegisterCompany Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);


$app->configure('auth');
// $app->configure('app');
$app->configure('service');
$app->configure('api');
/*
|--------------------------------------------------------------------------
| RegisterCompany Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\ApiResponseServiceProvider::class);



/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/
$app->withFacades();
$app->withEloquent();

$app->alias('cache', \Illuminate\Cache\CacheManager::class);

$app->configure('mail');

$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);


$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'client' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
    'throttle' => Aaranda\LumenPassportMultiauth\Http\Middleware\ThrottleRequests::class,
    'scopes' => Laravel\Passport\Http\Middleware\CheckScopes::class,
    'scope' => Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
    "checkAuthorizedCompanyAndClient" => App\Http\Middleware\CheckAuthorizedCompanyAndClient::class,
    "checkAuthorizedAdmin" => App\Http\Middleware\CheckAuthorizedAdmin::class,


]);
$app->register('Maatwebsite\Excel\ExcelServiceProvider');

$app->register(\Bavix\Wallet\WalletServiceProvider::class);
$app->register(Irazasyed\Larasupport\Providers\ArtisanServiceProvider::class);


$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(\Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(Anik\Form\FormRequestServiceProvider::class);
$app->register(Spatie\Activitylog\ActivitylogServiceProvider::class);
// $app->register(Illuminate\Mail\MailServiceProvider::class);
// $app->register(\ESolution\DBEncryption\Providers\DBEncryptionServiceProvider::class);
$app->register(\Illuminate\Mail\MailServiceProvider::class);


$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/api.php';
});

return $app;
