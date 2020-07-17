<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

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
     * @param User|User[] $user
     */
    protected function transform($users): array
    {
        if (!is_array($users)) {
            $users = [$users];
        }
        return array_map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
        ], $users);
    }
}
