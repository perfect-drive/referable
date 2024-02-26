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
        $className = Str::of((string) Route::current()?->uri())
            ->after('spa/referable/')
            ->studly()
            ->toString();

        $referencable = $this->findReferencable($className);

        if ($referencable && method_exists($referencable, 'getReferenceCollection')) {
            return response()->json(
                $referencable::getReferenceCollection()
            );
        }

        return response()->json([]);
    }

    private function findReferencable(string $className): ?string
    {
        return ReferableFinder::all()
            ->first(fn ($class) => class_basename($class) === $className);
    }
}
