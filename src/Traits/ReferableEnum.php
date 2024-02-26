<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Traits;

use Illuminate\Support\Collection;

trait ReferableEnum
{
    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     */
    public static function getReferenceCollection(): Collection
    {
        if (self::getReferenceValue() === null) {
            return collect();
        }

        return collect(self::cases())
            ->map(fn ($case) => [
                config('referable.key_name') => $case->{self::getReferenceValue()},
                config('referable.value_name') => $case->{self::getReferenceTitle()}(),
                ...self::getAdditionalReferenceAttributes(),
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
