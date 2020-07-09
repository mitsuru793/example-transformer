<?php
declare(strict_types=1);

namespace Php\Domain\User;

interface UserRepository
{
    public function find(int $id): ?User;

    public function paging(int $page, int $perPage): array;

    public function create(User $user): User;
}
