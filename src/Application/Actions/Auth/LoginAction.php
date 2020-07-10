<?php
declare(strict_types=1);

namespace Php\Application\Actions\Auth;

use Php\Application\Middlewares\LoginAuth;
use Psr\Http\Message\ResponseInterface as Response;

final class LoginAction extends AuthAction
{
    protected function action(): Response
    {
        $userId = 1;
        return $this->response
            ->withAddedHeader('Set-Cookie', sprintf('%s=%d', LoginAuth::SESSION_KEY, $userId))
            ->withAddedHeader('Location', $this->request->getServerParams()['HTTP_REFERER'])
            ->withStatus(303);
    }
}
