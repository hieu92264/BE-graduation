<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * service, repo
     */
    public function register(): void
    {
        //
    }

    public function registerServices(): void
    {
        //auth
    }

    public function registerRepositories(): void
    {
        //auth
    }

    /**
     * Bootstrap any application services.
     * view, route, schema, validate
     */
    public function boot(): void {}
}
