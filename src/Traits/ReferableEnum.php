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
        if (self::getReferenceValue() === null) {
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
            ->map(fn ($case) => [
                config('referable.key_name') => $case->{self::getReferenceValue()},
                config('referable.value_name') => $case->{self::getReferenceTitle()}(),
                ...collect(self::getAdditionalReferenceAttributes())->mapWithKeys(fn ($value, $key) => [$key => $case->{$value}()])
            ]);
    }

    public static function getReferenceValue(): ?string
    {
        return 'value';
    }

    public static function getReferenceTitle(): string
    {
        return 'name';
    }

    /**
     * @return array<string, string>
     */
    public static function getAdditionalReferenceAttributes(): array
    {
        return [];
    }
}
