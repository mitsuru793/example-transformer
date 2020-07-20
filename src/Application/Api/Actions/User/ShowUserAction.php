<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Application\ActionError;
use Php\Application\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;

final class ShowUserAction extends UserAction
{
    protected function action(): Response
    {
        $id = (int)$this->resolveArg('userId');
        $user = $this->userRepo->find($id);
        if (is_null($user)) {
            $msg = "Not found user of $id.";
            $err = new ActionError(ActionError::NOT_FOUND, $msg);
            $payload = new ActionPayload(404, null, $err);
            return $this->respond($payload);
        }
        return $this->respondWithData($this->transform($user));
    }
}
