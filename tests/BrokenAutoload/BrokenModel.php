<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\BrokenAutoload;

use Illuminate\Support\Collection;
use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\ReferableFinder\ReferableFinder;

/**
 * Fixture for the {@see ReferableFinder}
 * resilience test.
 *
 * Extends a class that doesn't exist, so any attempt to autoload this file
 * throws an `Error: Class "..." not found`. The finder's try/catch must
 * skip this class without crashing and still register routes for the
 * other (valid) classes scanned in the same pass.
 *
 * This file lives in its own subdirectory (`tests/BrokenAutoload/`) so the
 * package's normal test setup — which only scans `tests/Enums` and
 * `tests/Models` — never tries to load it. The resilience test points the
 * finder at this directory explicitly.
 */
class BrokenModel extends NonExistentParent implements ReferableInterface
{
    public static function getReferenceCollection(?string $scopeName = null): Collection
    {
        return collect();
    }

    public static function getReferenceKey(): ?string
    {
        return null;
    }

    public static function getReferenceValue(): string
    {
        return 'name';
    }

    public static function getReferenceSortBy(): string
    {
        return 'id';
    }

    public static function getAdditionalReferenceAttributes(): array
    {
        return [];
    }
}
