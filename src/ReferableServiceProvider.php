<?php

namespace PerfectDrive\Referable;

use PerfectDrive\Referable\Controllers\ReferableController;
use PerfectDrive\Referable\ReferableFinder\ReferableFinder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReferableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('referable')
            ->hasConfigFile();
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        // Register a 'referable' route for each referable model and enum (class using the Referable trait)
        collect(ReferableFinder::all())
            ->each(function ($class) {
                if (is_string($class)) {
                    $route = Str::snake(class_basename($class));
                    Route::get(config('referable.url').$route, ReferableController::class);
                }
            });
    }
}
