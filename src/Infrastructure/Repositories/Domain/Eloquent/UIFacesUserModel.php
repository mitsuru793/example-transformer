<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent;

use Php\Domain\Models\Domainable;
use Php\Domain\UIFacesUser\UIFacesUser;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $position
 * @property string $photo_url
 * @property string $photo_file
 */
final class UIFacesUserModel extends EloquentBaseModel implements Domainable
{
    /** @var string */
    protected $table = 'ui_faces_users';

    /** @var array */
    protected $fillable = [
        'id',
        'data',
    ];

    public function toDomain(): UIFacesUser
    {
        return new UIFacesUser(
            $this->id,
            $this->name,
            $this->email,
            $this->position,
            $this->photo_url,
            $this->photo_file,
        );
    }
}
