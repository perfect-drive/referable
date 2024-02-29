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
        if (self::getReferenceValue() === null) {
            return collect();
        }

        $model = self::getModel();

        if ($scopeName && ! $model->hasMethod('scope').Str::studly($scopeName)) {
            $scopeName = null;
        }

        return $model->orderBy(self::getReferenceTitle())
            ->when($scopeName, fn ($query) => $query->{$scopeName}())
            ->get()
            ->map(fn ($model) => [
                config('referable.key_name') => $model->{self::getReferenceValue()},
                config('referable.value_name') => $model->{self::getReferenceTitle()},
                ...collect(self::getAdditionalReferenceAttributes())->mapWithKeys(fn ($value, $key) => [$key => $model->{$value}])
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
