<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Application\ActionError;
use Php\Application\ActionPayload;
use Php\Application\Api\Actions\Action;
use Php\Domain\User\User;
use Php\Domain\User\UserRepository;

abstract class UserAction extends Action
{
    protected UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @throws \League\Route\Http\Exception\BadRequestException
     */
    protected function findUserFromPath()
    {
        $id = (int)$this->resolveArg('userId');
        return $this->userRepo->find($id);
    }

    protected function respondNotFoundUser()
    {
        $id = $this->resolveArg('userId');
        $msg = "Not found user of $id.";
        $err = new ActionError(ActionError::NOT_FOUND, $msg);
        $payload = new ActionPayload(404, null, $err);
        return $this->respond($payload);
    }

    /**
     * @param User|User[] $user
     */
    protected function transform($user): array
    {
        $transform = fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
        ];
        if (!is_array($user)) {
            return $transform($user);
        }

        return array_map($transform, $user);
    }
}
