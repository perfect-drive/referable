<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Enums;

use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\Traits\ReferableEnum;

enum AlternativeReferableEnum: int implements ReferableInterface
{
    use ReferableEnum;

    case FIRST = 1;

    case SECOND = 2;

    public function uuid(): string
    {
        return match ($this) {
            self::FIRST => '00000000-0000-0000-0000-000000000001',
            self::SECOND => '00000000-0000-0000-0000-000000000002',
        };
    }

    public function title(): string
    {
        return match ($this) {
            self::FIRST => 'Alternative Enum 1',
            self::SECOND => 'Alternative Enum 2',
        };
    }

    public function ordering(): int
    {
        return match ($this) {
            self::FIRST => 2,
            self::SECOND => 1,
        };
    }

    public function additionalAttribute(): string
    {
        return match ($this) {
            self::FIRST => 'Test Attribute 1',
            self::SECOND => 'Test Attribute 2',
        };
    }

    public static function getReferenceKey(): ?string
    {
        return 'uuid';
    }

    public static function getReferenceValue(): string
    {
        return 'title';
    }

    public static function getReferenceSortBy(): string
    {
        return 'ordering';
    }

    /**
     * @return array<string, string>
     */
    public static function getAdditionalReferenceAttributes(): array
    {
        return [
            'attribute' => 'additionalAttribute',
        ];
    }
}
