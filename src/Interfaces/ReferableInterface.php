<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Interfaces;

use Illuminate\Support\Collection;
use PerfectDrive\Referable\Traits\ReferableEnum;

interface ReferableInterface
{
    /**
     * @return Collection<int, class-string<object>>
     */
    public static function getReferenceCollection(): Collection;

    public static function getReferenceKey(): ?string;

    /**
     * Name of the method (for enums) or attribute (for models) on each case /
     * row that produces the human-readable value. The trait for enums calls
     * this dynamically as `$case->{...}()`; the trait for models reads it as
     * `$model->{...}`. Override per consumer to pin a specific name.
     *
     * Defaults: `'name'` for models; for enums `'name'` when a `name()`
     * method is defined, otherwise `'label'`-if-defined-else-`'name'` —
     * see {@see ReferableEnum::getReferenceValue()}.
     */
    public static function getReferenceValue(): string;

    public static function getReferenceSortBy(): string;

    /**
     * @return array<string, string>
     */
    public static function getAdditionalReferenceAttributes(): array;
}
