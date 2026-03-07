<?php

namespace App\Providers;

use App\Http\Interfaces\AuthServiceInterface;
use App\Http\Interfaces\CategoryServiceInterface;
use App\Http\Interfaces\EmployeeServiceInterface;
use App\Http\Interfaces\PermissionServiceInterface;
use App\Http\Interfaces\UserProfileServiceInterface;
use App\Http\Interfaces\UserServiceInterface;
use App\Http\Services\AuthService;
use App\Http\Services\CategoryService;
use App\Http\Services\EmployeeService;
use App\Http\Services\PermissionService;
use App\Http\Services\UserProfileService;
use App\Http\Services\UserService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
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
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(PermissionServiceInterface::class, PermissionService::class);
        $this->app->singleton(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->singleton(PermissionServiceInterface::class, PermissionService::class);
        $this->app->singleton(UserProfileServiceInterface::class, UserProfileService::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(CategoryServiceInterface::class, CategoryService::class);
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
        Date::use(CarbonImmutable::class);
    }
}
