<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class ListUsersAction extends UserAction
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    protected function action(): Response
    {
        $users = $this->userRepo->paging(1, 5);
        return $this->respondWithData($users);
    }
}