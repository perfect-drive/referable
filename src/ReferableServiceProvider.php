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

    public function bootingPackage(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $middleware = config('referable.middleware');

        if (! is_array($middleware)) {
            $middleware = null;
        }

        /** @var array<string>|null $middleware */
        $baseUrl = config('referable.base_url', 'spa/referable/');

        if (! is_string($baseUrl)) {
            $baseUrl = 'spa/referable/';
        }

        // Register a 'referable' route for each referable model and enum (class implementing ReferableInterface)
        // and each referable scope within these models and enums
        collect(ReferableFinder::all())
            ->each(function ($className) use ($middleware, $baseUrl) {
                // Register the base route
                $route = Str::snake(class_basename($className));
                Route::get($baseUrl.$route, ReferableController::class)
                    ->middleware($middleware);

                // Register a route for each referable scope
                $class = new ReflectionClass($className);

                collect($class->getMethods())
                    ->filter(fn (ReflectionMethod $method) => collect($method->getAttributes())
                        ->map(fn (ReflectionAttribute $attribute) => $attribute->getName())
                        ->contains(ReferableScope::class),
                    )
                    ->each(function (ReflectionMethod $method) use ($route, $middleware, $baseUrl) {
                        $scopeRoute = self::scopeRouteName($method->getName());
                        Route::get($baseUrl.$route.'/'.$scopeRoute, ReferableController::class)
                            ->middleware($middleware);
                    });
            });
    }

    /**
     * Resolve the public route segment for a scope method, stripping the legacy
     * "scope" prefix when present. Methods using Laravel's #[Scope] attribute
     * carry no prefix and are used as-is.
     */
    protected static function scopeRouteName(string $method): string
    {
        $name = Str::startsWith($method, 'scope') && ctype_upper(substr($method, 5, 1))
            ? Str::after($method, 'scope')
            : $method;

        return Str::snake($name);
    }
}
