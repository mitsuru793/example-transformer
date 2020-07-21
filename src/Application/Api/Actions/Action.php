<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions;

use League\Route\Http\Exception\BadRequestException;
use Php\Application\ActionError;
use Php\Application\ActionPayload;
use Php\Application\ActionTrait;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Action
{
    use ActionTrait;

    /**
     * @return array|object
     * @throws BadRequestException
     */
    protected function getFormData()
    {
        $input = json_decode(file_get_contents('php://input'));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestException('Malformed JSON input.');
        }
        return $input;
    }

    /**
     * @param array<mixed, mixed> $input
     * @param array<string, mixed> $rules
     * @return array<mixed, mixed>
     */
    protected function validateInputs(array $input, array $rules): array
    {
        $v = new \Valitron\Validator($input);
        $v->mapFieldsRules($rules);
        $v->stopOnFirstFail();
        $v->validate();
        return $v->errors();
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null): Response
    {
        $payload = new ActionPayload(200, $data);
        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);
        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }

    protected function respondValidatedErrors(array $errors): Response
    {
        $key = array_key_first($errors);
        $msg = $errors[$key][0];
        $err = new ActionError(ActionError::UNPROCESSABLE_ENTITY, $msg);
        $payload = new ActionPayload(422, null, $err);
        return $this->respond($payload);
    }
}
