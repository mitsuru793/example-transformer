<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent;

/**
 * @property string $name
 * @property string $email
 * @property string $position
 * @property string $photoUrl
 */
final class UIFacesUserModel extends EloquentBaseModel
{
    /** @var string */
    protected $table = 'ui_faces_users';

    /** @var array */
    protected $fillable = [
        'id',
        'data',
    ];
}
