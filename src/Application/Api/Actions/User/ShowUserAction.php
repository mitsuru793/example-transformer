<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class ShowUserAction extends UserAction
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    protected function action(): Response
    {
        $id = (int)$this->resolveArg('userId');
        $user = $this->userRepo->find($id);
        return $this->respondWithData($user);
    }
}
