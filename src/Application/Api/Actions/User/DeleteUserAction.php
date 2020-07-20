<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Application\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;

final class DeleteUserAction extends UserAction
{
    protected function action(): Response
    {
        $id = (int)$this->resolveArg('userId');
        $this->userRepo->delete($id);
        return $this->respond(new ActionPayload(204));
    }
}
