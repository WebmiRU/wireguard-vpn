<?php

namespace App\Providers;

use App\Interfaces\ConfigServiceInterface;
use App\Interfaces\KeyServiceInterface;
use App\Interfaces\WireguardServiceInterface;
use App\Services\ConfigService;
use App\Services\KeyService;
use App\Services\WireguardService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(KeyServiceInterface::class, function ($app) {
            return new KeyService();
        });

        $this->app->singleton(ConfigServiceInterface::class, function ($app) {
            return new ConfigService();
        });

        $this->app->singleton(WireguardServiceInterface::class, function ($app) {
            return new WireguardService();
        });
    }
}
