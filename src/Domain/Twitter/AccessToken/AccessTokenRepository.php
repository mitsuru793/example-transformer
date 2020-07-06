<?php
declare(strict_types=1);

namespace Php\Domain\Twitter\AccessToken;

interface AccessTokenRepository
{
    public function findByTwitterUserId(int $userId): ?AccessToken;

    public function createOrUpdate(AccessToken $token): AccessToken;
}
