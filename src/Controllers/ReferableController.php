<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PerfectDrive\Referable\ReferableFinder\ReferableFinder;

class ReferableController
{
    public function __invoke(): JsonResponse
    {
        $baseUrl = config('referable.base_url', '/spa/referable/');

        if (! is_string($baseUrl)) {
            $baseUrl = '/spa/referable/';
        }

        $referable = Str::after((string) Route::current()?->uri(), $baseUrl);

        $className = Str::of($referable)->before('/')->studly()->toString();
        $scopeName = Str::contains($referable, '/')
            ? Str::of($referable)
                ->after('/')
                ->camel()
                ->toString()
            : null;

        $referable = $this->findReferable($className);

        if (! $referable || ! method_exists($referable, 'getReferenceCollection')) {
            return response()->json();
        }

        return response()->json(
            $referable::getReferenceCollection($scopeName)
        );
    }

    private function findReferable(string $className): ?string
    {
        return ReferableFinder::all()
            ->first(fn ($class) => class_basename($class) === $className);
    }
}
