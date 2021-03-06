<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

final class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $input = $this->request->getParsedBody();
        $errors = $this->validateInputs($input, [
            'name' => [
                'required',
                ['lengthBetween', 3, 30]
            ],
            'password' => [
                'required',
                ['lengthBetween', 8, 16]
            ],
        ]);
        if ($errors) {
            return $this->respondValidatedErrors($errors);
        }

        $user = new User(null, $input['name'], $input['password']);
        $created = $this->userRepo->create($user);
        return $this->respondWithData($this->transform($created));
    }
}