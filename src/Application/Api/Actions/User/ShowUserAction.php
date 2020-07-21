<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

final class ShowUserAction extends UserAction
{
    protected function action(): Response
    {
        $user = $this->findUserFromPath();
        if (is_null($user)) {
            return $this->respondNotFoundUser();
        }
        return $this->respondWithData($this->transform($user));
    }
}
