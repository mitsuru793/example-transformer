<?php
declare(strict_types=1);

namespace Php\Library\UIFaces;

use GuzzleHttp\Client as GuzzleClient;

final class Client
{
    private const BASE = 'https://uifaces.co/api';

    private GuzzleClient $client;

    public function __construct(string $apiKey)
    {
        $this->client = new GuzzleClient([
            'headers' => [
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ],
        ]);
    }

    public function getRaw(Parameters $params): array
    {
        $response = $this->client->get(self::BASE . '?' . $params->toHttpQuery());
        $code = $response->getStatusCode();
        if ($code !== 200) {
            throw new \RuntimeException("Request failed on UIFaces api. HTTP Status Code is $code.");
        }

        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        if (array_key_exists('error', $data)) {
            throw new \RuntimeException($data['error']);
        }

        return $data;
    }

    /**
     * @return User[]
     */
    public function getUsers(Parameters $params): array
    {
        $rows = $this->getRaw($params);
        return array_map(function (array $row) {
            $user = new User();
            $user->name = $row['name'];
            $user->email = $row['email'];
            $user->position = $row['position'];
            $user->photo = $row['photo'];
            return $user;
        }, $rows);
    }
}
