<?php
declare(strict_types=1);

namespace Php\Application\Actions\Auth;

use League\Plates\Engine;
use Php\Application\Middlewares\LoginAuth;
use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class LoginAction extends AuthAction
{
    private UserRepository $userRepo;

    public function __construct(Engine $templates, UserRepository $userRepo)
    {
        parent::__construct($templates);
        $this->userRepo = $userRepo;
    }

    protected function action(): Response
    {
        $params = $this->request->getParsedBody();
        if (empty($userName = $params['userName']) ||
            empty($password = $params['password'])) {
            // TODO data format
            throw new  \RuntimeException('Failed login.');
        }

        $user = $this->userRepo->findByNameAndPassword($userName, $password);
        if (is_null($user)) {
            return $this->response
                ->withAddedHeader('Location', $this->request->getServerParams()['HTTP_REFERER'])
                ->withStatus(301); // TODO modify to 401
        }

        return $this->response
            ->withAddedHeader('Set-Cookie', sprintf('%s=%d', LoginAuth::SESSION_KEY, $user->id))
            ->withAddedHeader('Location', $this->request->getServerParams()['HTTP_REFERER'])
            ->withStatus(302);
    }
}
