<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Enums;

use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\Traits\ReferableEnum;

/**
 * Fixture for the both-defined case: when a consuming enum defines BOTH
 * `name()` and `label()`, the trait's auto-detect picks `name()` (precedence
 * kept backwards-compatible). The deliberately-contrasting return values
 * ("NAME_xxx" vs "Label xxx") let the assertion distinguish which method was
 * actually called.
 */
enum FullyDefinedReferableEnum: int implements ReferableInterface
{
    use ReferableEnum;

    case ONE = 1;

    case TWO = 2;

    public function name(): string
    {
        return match ($this) {
            self::ONE => 'NAME_ONE',
            self::TWO => 'NAME_TWO',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ONE => 'Label One',
            self::TWO => 'Label Two',
        };
    }
}
