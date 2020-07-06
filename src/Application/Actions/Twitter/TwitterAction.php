<?php
declare(strict_types=1);

namespace Php\Application\Actions\Twitter;

use Php\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TwitterAction extends Action
{
    private const AUTH_KEY = 'twitter_auth';

    protected function setOauthToken(Response $res, string $value): Response
    {
        return $this->response->withAddedHeader(
            'Set-Cookie',
            sprintf('%s=%s; Expires=%s; Path=/',
                self::AUTH_KEY, $value,
                date('D, d-M-Y H:i:s', time() + 60 * 5) . ' GMT',
            )
        );
    }

    /**
     * returned includes
     *   + oauth_consumer_key
     *   + oauth_token
     *   + oauth_verifier
     */
    protected function getOauthToken(Request $req): string
    {
        return $req->getCookieParams()[self::AUTH_KEY] ?? '';
    }
}