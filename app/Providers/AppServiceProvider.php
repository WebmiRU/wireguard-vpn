<?php

namespace App\Providers;

use App\Interfaces\ConfigServiceInterface;
use App\Interfaces\KeyServiceInterface;
use App\Services\ConfigService;
use App\Services\KeyService;
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
    }
}
