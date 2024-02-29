<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Controllers;

use PerfectDrive\Referable\ReferableFinder\ReferableFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ReferableController
{
    public function __invoke(): JsonResponse
    {
        $referable = Str::after((string) Route::current()?->uri(), config('referable.base_url'));

        $className = Str::of($referable)->before('/')->studly()->toString();
        $scopeName = Str::contains($referable, "/")
            ? Str::of($referable)
                ->after("/")
                ->camel()
                ->toString()
            : null;

        $referable = $this->findReferable($className);

        if (!$referable || ! method_exists($referable, 'getReferenceCollection')) {
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
