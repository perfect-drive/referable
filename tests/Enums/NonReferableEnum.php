<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Enums;

enum NonReferableEnum: int
{
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
}
