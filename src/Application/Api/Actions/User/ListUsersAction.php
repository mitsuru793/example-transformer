<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class ListUsersAction extends UserAction
{
    protected function action(): Response
    {
        $users = $this->userRepo->paging(1, 5);
        return $this->respondWithData($users);
    }
}
