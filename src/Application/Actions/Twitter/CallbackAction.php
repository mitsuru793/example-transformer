<?php
declare(strict_types=1);

namespace Php\Application\Actions\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use League\Plates\Engine;
use Php\Application\Actions\ActionError;
use Php\Application\Actions\ActionPayload;
use Php\Domain\Twitter\AccessToken\AccessToken;
use Php\Domain\Twitter\AccessToken\AccessTokenRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class CallbackAction extends TwitterAction
{
    private TwitterOAuth $twitter;

    private AccessTokenRepository $accessTokenRepo;

    public function __construct(Engine $templates, TwitterOAuth $twitter, AccessTokenRepository $accessTokenRepo)
    {
        parent::__construct($templates);
        $this->twitter = $twitter;
        $this->accessTokenRepo = $accessTokenRepo;
    }

    protected function action(): Response
    {
        $token = $this->request->getQueryParams()['oauth_token'] ?? null;
        if (empty($token)) {
            $err = new ActionError(ActionError::SERVER_ERROR, 'Not found oauth token in request.');
            return $this->respond(new ActionPayload(500, null, $err));
        }

        $verifier = $this->request->getQueryParams()['oauth_verifier'] ?? null;
        if (is_null($verifier)) {
            $err = new ActionError(ActionError::SERVER_ERROR, 'Not found verifier of twitter.');
            $this->respond(new ActionPayload(500, null, $err));
        }

        /**
         * Response includes the following:
         * + oauth_token
         * + oauth_token_secret
         * + user_id
         * + screen_name
         */
        $res = $this->twitter->oauth('oauth/access_token', [
            'oauth_consumer_key' => getenv('TWITTER_CONSUMER_KEY'),
            'oauth_token' => $token,
            'oauth_verifier' => $verifier,
        ]);
        $accessToken = AccessToken::fromTwitterResponse($res);
        $this->accessTokenRepo->createOrUpdate($accessToken);

        $html = 'insert access token: ' . json_encode($accessToken);
        $this->renderView($this->response,
            'debug/render-html', compact('html') + ['loginUser' => $this->loginUser]
        );
        return $this->response;
    }
}