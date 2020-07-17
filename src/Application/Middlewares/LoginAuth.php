<?php
declare(strict_types=1);

namespace Php\Application\Middlewares;

use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class LoginAuth implements MiddlewareInterface
{
    public const SESSION_KEY = 'session_id';
    public const ATTRIBUTE_KEY = 'loginUser';

    /**@var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        // TODO user idを暗号化
        $id = $request->getCookieParams()[self::SESSION_KEY] ?? null;
        if (is_null($id)) {
            return $handler->handle($request);
        }

        $user = $this->userRepository->find((int)$id);
        $request = $request->withAttribute(self::ATTRIBUTE_KEY, $user);
        return $handler->handle($request);
    }
}
