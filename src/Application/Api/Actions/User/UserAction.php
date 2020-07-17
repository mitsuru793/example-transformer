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
    protected function transform($user): array
    {
        $transform = fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
        ];
        if (!is_array($user)) {
           return $transform($user) ;
        }

        return array_map($transform, $user);
    }
}
