<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

final class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $user = new User((int)$body['id'], $body['name'], $body['password']);
        $created = $this->userRepo->create($user);
        return $this->respondWithData($this->transform($created));
    }
}