<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ReferableModel
{
    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     */
    public static function getReferenceCollection(?string $scopeName = null): Collection
    {
        if (self::getReferenceKey() === null) {
            return collect();
        }

        if ($scopeName && ! method_exists(self::class, 'scope'.Str::studly($scopeName))) {
            $scopeName = null;
        }

        $model = self::getModel();

        return $model
            ->orderBy(self::getReferenceSortBy())
            ->when($scopeName, fn ($query) => $query->{$scopeName}())
            ->get()
            ->map(fn ($model) => [
                config('referable.key_name') => $model->{self::getReferenceKey()},
                config('referable.value_name') => $model->{self::getReferenceValue()},
                ...collect(self::getAdditionalReferenceAttributes())->mapWithKeys(fn ($value, $key) => [$key => $model->{$value}]),
            ])
            ->values();
    }

    public static function getReferenceKey(): ?string
    {
        return self::getModel()->getKeyName();
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
