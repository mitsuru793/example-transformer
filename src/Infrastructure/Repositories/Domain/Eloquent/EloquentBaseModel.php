<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string id
 * @property string data
 */
abstract class EloquentBaseModel extends Model
{
    /** @var array */
    protected $fillable = [
        'id',
        'data,
    '];

    public $timestamps = false;
}
