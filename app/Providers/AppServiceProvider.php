<?php

namespace App\Providers;

use App\Domains\CarbonEmissions\Services\CarbonEmissionServiceInterface;
use App\Domains\CarbonEmissions\Services\CarbonEmissionService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
