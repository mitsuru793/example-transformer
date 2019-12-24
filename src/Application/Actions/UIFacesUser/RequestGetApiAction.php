<?php
declare(strict_types=1);

namespace Php\Application\Actions\UIFacesUser;

use Php\Domain\UIFacesUser\UIFacesUser;
use Php\Domain\UIFacesUser\UIFacesUserRepository;
use Php\Library\UIFaces\Client;
use Php\Library\UIFaces\User;
use Psr\Http\Message\ResponseInterface as Response;

final class RequestGetApiAction extends UIFacesUserAction
{
    private UIFacesUserRepository $UIFacesUserRepository;

    private Client $UIFacesClient;

    public function __construct(UIFacesUserRepository $UIFacesUserRepository, Client $UIFacesClient)
    {
        $this->UIFacesUserRepository = $UIFacesUserRepository;
        $this->UIFacesClient = $UIFacesClient;
    }

    protected function action(): Response
    {
        $users = [];
        $genders = ['male', 'female'];
        foreach ($genders as $gender) {
            $params = new \Php\Library\UIFaces\Parameters();
            $params->limit(5)->genders([$gender]);
            $newUsers = $this->UIFacesClient->getUsers($params);
            $users = array_merge($users, $newUsers);
        }

        $users = array_map(fn(User $user) => new UIFacesUser(
            null, $user->name, $user->email, $user->position, $user->photo,
        ), $users);

        $this->UIFacesUserRepository->createMany($users);

        return $this->response;
    }
}
