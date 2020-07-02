<?php
declare(strict_types=1);

namespace Php\Application\Actions\Auth;

use Psr\Http\Message\ResponseInterface as Response;

final class PermitPreflightAction extends AuthAction
{
    protected function action(): Response
    {
        return $this->response
            ->withAddedHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE')
            ->withAddedHeader('Access-Control-Allow-Headers', 'Content-Type');
    }
}