<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions\User;

use Php\Application\ActionError;
use Php\Application\ActionPayload;
use Php\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;

final class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $input = $this->request->getParsedBody();

        $v = new \Valitron\Validator($input);
        $v->mapFieldsRules([
            'name' => [
                'required',
                ['lengthBetween', 3, 30]
            ],
            'password' => [
                'required',
                ['lengthBetween', 8, 16]
            ],
        ]);
        $v->stopOnFirstFail();
        $v->validate();
        if ($errors = $v->errors()) {
            $key = array_key_first($errors);
            $msg = $errors[$key][0];
            $err = new ActionError(ActionError::UNPROCESSABLE_ENTITY, $msg);
            $payload = new ActionPayload(422, null, $err);
            return $this->respond($payload)->withStatus(422);
        }

        $user = new User(null, $input['name'], $input['password']);
        $created = $this->userRepo->create($user);
        return $this->respondWithData($this->transform($created));
    }
}