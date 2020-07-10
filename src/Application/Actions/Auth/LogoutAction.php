<?php
declare(strict_types=1);

namespace Php\Application\Actions\Auth;

use Php\Application\Middlewares\LoginAuth;
use Psr\Http\Message\ResponseInterface as Response;

final class LogoutAction extends AuthAction
{
    protected function action(): Response
    {
        return $this->response
            ->withAddedHeader('Set-Cookie', sprintf(
                '%s=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0;',
                LoginAuth::SESSION_KEY,
            ))
            ->withAddedHeader('Location', $this->request->getServerParams()['HTTP_REFERER'])
            ->withStatus(303);
    }
}
