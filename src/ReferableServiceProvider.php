<?php

namespace PerfectDrive\Referable;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PerfectDrive\Referable\Attributes\ReferableScope;
use PerfectDrive\Referable\Controllers\ReferableController;
use PerfectDrive\Referable\ReferableFinder\ReferableFinder;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReferableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('referable')
            ->hasConfigFile()
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile();
            });
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $middleware = config('referable.middleware');

        if (! is_array($middleware)) {
            $middleware = null;
        }

        // Register a 'referable' route for each referable model and enum (class implementing ReferableInterface)
        // and each referable scope within these models and enums
        collect(ReferableFinder::all())
            ->each(function ($className) use ($middleware) {
                if (is_string($className)) {
                    // Register the base route
                    $route = Str::snake(class_basename($className));
                    Route::get(config('referable.base_url').$route, ReferableController::class)
                        ->middleware($middleware);

                    // Register a route for each referable scope
                    /** @var class-string $className */
                    $class = new ReflectionClass($className);

                    collect($class->getMethods())
                        ->filter(fn (ReflectionMethod $method) => collect($method->getAttributes())
                            ->map(fn (ReflectionAttribute $attribute) => $attribute->getName())
                            ->contains(ReferableScope::class),
                        )
                        ->each(function ($method) use ($route, $middleware) {
                            $scopeRoute = Str::snake(Str::after($method->getName(), 'scope'));
                            Route::get(config('referable.base_url').$route.'/'.$scopeRoute, ReferableController::class)
                                ->middleware($middleware);
                        });
                }
            });
    }
}
