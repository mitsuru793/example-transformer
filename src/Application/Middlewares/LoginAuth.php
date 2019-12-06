<?php
declare(strict_types=1);

namespace Php\Application\Middlewares;

use Php\Infrastructure\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class LoginAuth
{
    public const SESSION_KEY = 'session_id';
    public const ATTRIBUTE_KEY = 'loginUser';

    /**@var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // TODO idを暗号化
        $id = $request->getQueryParams()[self::SESSION_KEY] ?? null;
        if (is_null($id)) {
            return $handler->handle($request);
        }
        assert(is_int($id));

        $user = $this->userRepository->findUserOfId($id);
        $request = $request->withAttribute(self::ATTRIBUTE_KEY, $user);
        return $handler->handle($request);
    }
}
