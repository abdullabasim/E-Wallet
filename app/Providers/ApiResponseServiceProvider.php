<?php

namespace App\Providers;

use App\src\ApiResponse;
use Illuminate\Support\ServiceProvider;
use App\src\Contracts\ApiInterface;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * RegisterCompany API class.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(ApiInterface::class, function () {
            return new ApiResponse();
        });
    }

    /**
     * Bootstrap API resources.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();

        $this->registerHelpers();

        $this->publishes([
            __DIR__ . '/../config/api.php' => config_path('api.php'),
        ], 'api-response');
    }

    /**
     * Set Config files.
     */
    protected function setupConfig()
    {
        $path = realpath($raw = __DIR__ . '/../../config/api.php') ?: $raw;
        $this->mergeConfigFrom($path, 'api');
    }

    /**
     * RegisterCompany helpers.
     */
    protected function registerHelpers()
    {
        if (file_exists($helperFile = __DIR__ . '/../Src/helpers.php')) {
            require_once $helperFile;
        }
    }
}
