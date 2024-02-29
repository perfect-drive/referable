<?php

namespace PerfectDrive\Referable;

use PerfectDrive\Referable\Attributes\ReferableScope;
use PerfectDrive\Referable\Controllers\ReferableController;
use PerfectDrive\Referable\ReferableFinder\ReferableFinder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
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
        // Register a 'referable' route for each referable model and enum (class implementing ReferableInterface)
        collect(ReferableFinder::all())
            ->each(function ($class) {
                if (is_string($class)) {
                    // Register the base route
                    $route = Str::snake(class_basename($class));
                    Route::get(config('referable.base_url').$route, ReferableController::class);

                    // Register a route for each referable scope
                    $class = new ReflectionClass($class);

                    collect($class->getMethods())
                        ->filter(fn(ReflectionMethod $method) =>
                            collect($method->getAttributes())
                                ->map(fn(ReflectionAttribute $attribute) => $attribute->getName())
                                ->contains(ReferableScope::class),
                            )
                        ->each(function ($method) use ($route) {
                            $scopeRoute = Str::snake(Str::after($method->getName(), 'scope'));
                            Route::get(config('referable.base_url').$route.'/'.$scopeRoute, ReferableController::class);
                        });
                }
            });
    }
}
