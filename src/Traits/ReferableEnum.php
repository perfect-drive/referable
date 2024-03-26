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

    public static function getReferenceValue(): string
    {
        return 'name';
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
