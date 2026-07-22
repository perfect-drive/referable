<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Enums;

use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\Traits\ReferableEnum;

/**
 * Fixture for the auto-detect path on `ReferableEnum::getReferenceValue()`.
 *
 * Defines `label()` instead of `name()` (the Filament / Nova convention,
 * and the reason PHP enums' built-in `name` property is left alone).
 * The trait must pick `label` up automatically without an explicit
 * `getReferenceValue()` override.
 */
enum LabelReferableEnum: int implements ReferableInterface
{
    use ReferableEnum;

    case ALPHA = 1;

    case BRAVO = 2;

    case CHARLIE = 3;

    public function label(): string
    {
        return match ($this) {
            self::ALPHA => 'Alpha',
            self::BRAVO => 'Bravo',
            self::CHARLIE => 'Charlie',
        };
    }
}
