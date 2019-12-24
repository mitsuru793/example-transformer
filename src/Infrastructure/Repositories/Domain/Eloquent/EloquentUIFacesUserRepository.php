<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent;

use Php\Domain\UIFacesUser\UIFacesUser;
use Php\Domain\UIFacesUser\UIFacesUserRepository;

final class EloquentUIFacesUserRepository implements UIFacesUserRepository
{
    public function createMany(array $users): void
    {
        $data = array_map(fn(UIFacesUser $user) => [
            'name' => $user->name,
            'email' => $user->email,
            'position' => $user->position,
            'photo_url' => $user->photoUrl,
        ], $users);
        UIFacesUserModel::insert($data);
    }
}
