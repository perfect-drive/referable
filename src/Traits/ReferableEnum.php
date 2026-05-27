<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ReferableEnum
{
    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     */
    public static function getReferenceCollection(?string $scopeName = null): Collection
    {
        if (self::getReferenceKey() === null) {
            return collect();
        }

        if ($scopeName && ! method_exists(self::class, $scopeName)) {
            $scopeName = null;
        }

        return collect(self::cases())
            ->when($scopeName, function (Collection $collection, string $scopeName) {
                $scopeName = Str::camel($scopeName);

                return $collection->filter(fn ($case) => (bool) $case->{$scopeName}());
            })
            ->sortBy(fn ($case) => self::getReferenceSortBy() === 'value' ? $case->value : $case->{self::getReferenceSortBy()}())
            ->map(fn ($case) => [
                config('referable.key_name') => self::getReferenceKey() === 'value' ? $case->value : $case->{self::getReferenceKey()}(),
                config('referable.value_name') => $case->{self::getReferenceValue()}(),
                ...collect(self::getAdditionalReferenceAttributes())->mapWithKeys(fn ($value, $key) => [$key => $case->{$value}()]),
            ])
            ->values();
    }

    public static function getReferenceKey(): ?string
    {
        return 'value';
    }

    /**
     * Method called on each case to produce the human-readable value.
     *
     * `name()` takes precedence whenever it is defined, so every enum that
     * works as referable today keeps its current behaviour — this default is
     * strictly backwards-compatible. Enums that expose no `name()` method
     * auto-detect `label()` (the Laravel-ecosystem convention used by
     * Filament / Nova) so they work without a per-enum override. Override
     * this method to pin one explicitly, e.g. to move an existing `name()`
     * enum to `label()` during a migration.
     *
     * This is the only `getReference*()` default that auto-detects;
     * `getReferenceKey()`, `getReferenceSortBy()` and
     * `getAdditionalReferenceAttributes()` assume the method they reference
     * exists. The asymmetry is intentional: `value` is the most common
     * override, and `label`-vs-`name` is the only place where a built-in
     * PHP-enum property (`name`) collides with a downstream convention.
     *
     * Note: `method_exists()` returns `false` for methods exposed via
     * `__call()`. An enum that provides `name()` only via `__call` will
     * resolve to `label()` (or the final `name` fallback). If you need that
     * pattern, override this method explicitly.
     */
    public static function getReferenceValue(): string
    {
        return method_exists(static::class, 'name') ? 'name'
             : (method_exists(static::class, 'label') ? 'label' : 'name');
    }

    public static function getReferenceSortBy(): string
    {
        return self::getReferenceKey();
    }

    /**
     * @return array<string, string>
     */
    public static function getAdditionalReferenceAttributes(): array
    {
        return [];
    }
}
