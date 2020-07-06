<?php
declare(strict_types=1);

namespace Php\Application\Actions\Tweet;

use Abraham\TwitterOAuth\TwitterOAuth;
use League\Plates\Engine;
use Php\Domain\Twitter\AccessToken\AccessTokenRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class ListUserHome extends TweetAction
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
        $name = $this->resolveArg('name');
        $userToken = $this->accessTokenRepo->findByScreenName($name);
        $this->twitter->setOauthToken($userToken->token, $userToken->secret);
        $tweets = $this->twitter->get("statuses/home_timeline", ["count" => 25, "exclude_replies" => true]);

        $this->renderView($this->response, 'debug/dump', [
            'loginUser' => $this->loginUser, 'data' => compact('tweets')
        ]);
        return $this->response;
    }
}