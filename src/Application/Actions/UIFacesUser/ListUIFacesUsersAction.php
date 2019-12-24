<?php
declare(strict_types=1);

namespace Php\Application\Actions\UIFacesUser;

use League\Plates\Engine;
use Php\Domain\UIFacesUser\UIFacesUserRepository;
use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class ListUIFacesUsersAction extends UIFacesUserAction
{
    private UserRepository $userRepository;

    private UIFacesUserRepository $UIFacesUserRepository;

    public function __construct(Engine $templates, UserRepository $userRepository, UIFacesUserRepository $UIFacesUserRepository)
    {
        parent::__construct($templates);
        $this->userRepository = $userRepository;
        $this->UIFacesUserRepository = $UIFacesUserRepository;
    }

    protected function action(): Response
    {
        $loginUser = $this->userRepository->find(1);
        $users = $this->UIFacesUserRepository->findAll();

        return $this->renderView($this->response, 'ui-faces-users/list.phtml', compact(
            'loginUser', 'users',
        ));
    }
}
