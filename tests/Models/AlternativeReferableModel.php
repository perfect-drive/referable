<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\Traits\ReferableModel;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 */
class AlternativeReferableModel extends Model implements ReferableInterface
{
    use ReferableModel;

    public static function getReferenceKey(): string
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

    public static function getAdditionalReferenceAttributes(): array
    {
        return [
            'attribute' => 'additional_attribute',
        ];
    }
}
