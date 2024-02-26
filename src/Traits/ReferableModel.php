<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Traits;

use Illuminate\Support\Collection;

trait ReferableModel
{
    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     */
    public static function getReferenceCollection(): Collection
    {
        if (self::getReferenceValue() === null) {
            return collect();
        }

        $model = self::getModel();

        return $model->orderBy('name')
            ->get()
            ->map(fn ($model) => [
                config('referable.key_name') => $model->{self::getReferenceValue()},
                config('referable.value_name') => $model->{self::getReferenceTitle()},
                ...self::getAdditionalReferenceAttributes(),
            ]);
    }

    public static function getReferenceValue(): ?string
    {
        return self::getModel()->getKeyName();
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
