<?php

namespace App\Providers;

use App\Http\Interfaces\IAuthService;
use App\Http\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * service, repo
     */
    public function register(): void
    {
        $this->registerServices();
        $this->registerRepositories();
    }

    protected function registerServices(): void
    {
        //auth
        $this->app->singleton(IAuthService::class, AuthService::class);
    }

    protected function registerRepositories(): void
    {
        //auth
    }

    /**
     * Bootstrap any application services.
     * view, route, schema, validate
     */
    public function boot(): void
    {
    }
}
