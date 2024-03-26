<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PerfectDrive\Referable\Attributes\ReferableScope;
use PerfectDrive\Referable\Interfaces\ReferableInterface;
use PerfectDrive\Referable\Traits\ReferableModel;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 */
class BasicReferableModel extends Model implements ReferableInterface
{
    use ReferableModel;

    /**
     * @param  Builder<BasicReferableModel>  $query
     * @return Builder<BasicReferableModel>
     */
    #[ReferableScope]
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @param  Builder<BasicReferableModel>  $query
     * @return Builder<BasicReferableModel>
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }
}
