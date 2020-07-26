<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

final class UpdateUserAction extends UserAction
{
    protected function action(): Response
    {
        $user = $this->findUserFromPath();
        if (is_null($user)) {
            return $this->respondNotFoundUser();
        }

        $input = $this->request->getParsedBody();
        $errors = $this->validateInputs($input, [
            'name' => [
                'required',
                ['lengthBetween', 3, 30]
            ],
        ]);
        if ($errors) {
            return $this->respondValidatedErrors($errors);
        }

        $user->name = $input['name'];
        $this->userRepo->update($user);
        return $this->respondWithData($this->transform($user));
    }
}