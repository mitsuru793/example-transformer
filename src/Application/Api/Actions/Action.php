<?php
declare(strict_types=1);

namespace Php\Application\Api\Actions;

use League\Route\Http\Exception\BadRequestException;
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
}
