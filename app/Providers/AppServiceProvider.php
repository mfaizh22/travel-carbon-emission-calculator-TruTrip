<?php

namespace App\Providers;

use App\Domains\CarbonEmissions\Services\CarbonEmissionServiceInterface;
use App\Domains\CarbonEmissions\Services\CarbonEmissionService;
use App\Domains\Users\Repository\UserRepositoryInterface;
use App\Domains\Users\Repository\UserRepository;
use App\Domains\Users\Services\AuthServiceInterface;
use App\Domains\Users\Services\AuthService;
use App\Externals\CarbonEmissions\Services\CarbonEmissionProviderServiceInterface;
use App\Externals\CarbonEmissions\Squake\Services\SquakeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the provider interface to the Squake implementation
        $this->app->bind(
            CarbonEmissionProviderServiceInterface::class,
            SquakeService::class
        );

        // Bind the service interface to the implementation
        $this->app->bind(
            CarbonEmissionServiceInterface::class,
            CarbonEmissionService::class
        );
        
        // Bind the user repository interface to the implementation
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        
        // Bind the auth service interface to the implementation
        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
