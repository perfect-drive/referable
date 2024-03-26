<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Enums;

use PerfectDrive\Referable\Attributes\ReferableScope;
use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\Traits\ReferableEnum;

enum BasicReferableEnum: int implements ReferableInterface
{
    use ReferableEnum;

    case FIRST = 1;

    case SECOND = 2;

    case THIRD = 3;

    public function name(): string
    {
        return match ($this) {
            self::FIRST => 'First',
            self::SECOND => 'Second',
            self::THIRD => 'Third',
        };
    }

    #[ReferableScope]
    public function active(): bool
    {
        return in_array($this, [
            self::FIRST,
            self::SECOND,
        ], true);
    }

    public function inactive(): bool
    {
        return $this === self::THIRD;
    }
}
