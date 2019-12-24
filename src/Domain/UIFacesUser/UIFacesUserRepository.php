<?php
declare(strict_types=1);

namespace Php\Domain\UIFacesUser;

interface UIFacesUserRepository
{
    /**
     * @param UIFacesUser[] $users
     */
    public function createMany(array $users): void;
}
