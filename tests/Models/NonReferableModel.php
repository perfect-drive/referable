<?php

declare(strict_types=1);

namespace PerfectDrive\Referable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 */
class NonReferableModel extends Model
{
}
