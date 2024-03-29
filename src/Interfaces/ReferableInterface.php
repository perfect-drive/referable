<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Interfaces;

use Illuminate\Support\Collection;

interface ReferableInterface
{
    /**
     * @return Collection<int, class-string<object>>
     */
    public static function getReferenceCollection(): Collection;

    public static function getReferenceKey(): ?string;

    public static function getReferenceValue(): string;

    public static function getReferenceSortBy(): string;

    /**
     * @return array<string, string>
     */
    public static function getAdditionalReferenceAttributes(): array;
}
