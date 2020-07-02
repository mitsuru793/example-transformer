<?php
declare(strict_types=1);

namespace Php\Application\Actions\UIFacesUser;

use GuzzleHttp\Client as GuzzleClient;
use Php\Domain\UIFacesUser\UIFacesUser;
use Php\Domain\UIFacesUser\UIFacesUserRepository;
use Php\Library\UIFaces\Client;
use Php\Library\UIFaces\User;
use Php\Library\Util\Path;
use Psr\Http\Message\ResponseInterface as Response;

final class RequestGetApiAction extends UIFacesUserAction
{
    private UIFacesUserRepository $UIFacesUserRepository;

    private Client $UIFacesClient;

    private GuzzleClient $httpClient;

    public function __construct(UIFacesUserRepository $UIFacesUserRepository, Client $UIFacesClient, GuzzleClient $httpClient)
    {
        $this->UIFacesUserRepository = $UIFacesUserRepository;
        $this->UIFacesClient = $UIFacesClient;
        $this->httpClient = $httpClient;
    }

    protected function action(): Response
    {
        /** @var UIFacesUser[] $users */
        $users = [];
        $genders = ['male', 'female'];
        foreach ($genders as $gender) {
            $params = new \Php\Library\UIFaces\Parameters();
            $params->limit(1)->genders([$gender]);
            $newUsers = $this->UIFacesClient->getUsers($params);
            $users = array_merge($users, $newUsers);
        }

        $users = array_map(function (User $user) {
            $faceUser = new UIFacesUser(null, $user->name, $user->email, $user->position, $user->photo, '');
            $faceUser->addPhotoFile();
            return $faceUser;
        }, $users);

        $this->UIFacesUserRepository->createMany($users);

        foreach ($users as $user) {
            $file = fopen(Path::webRoot() . $user->photoFilePath(), 'w');
            $this->httpClient->get($user->photoUrl, ['sink' => $file]);
        }

        return $this->redirectBack($this->request, $this->response);
    }
}
